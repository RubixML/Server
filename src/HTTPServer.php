<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\Server\Models\Dashboard;
use Rubix\Server\Services\Scheduler;
use Rubix\Server\Services\Subscriptions;
use Rubix\Server\Services\EventBus;
use Rubix\Server\Services\Router;
use Rubix\Server\Services\Routes;
use Rubix\Server\Services\SSEChannel;
use Rubix\Server\HTTP\Middleware\Server\Middleware;
use Rubix\Server\HTTP\Middleware\Internal\DispatchEvents;
use Rubix\Server\HTTP\Middleware\Internal\AttachServerHeaders;
use Rubix\Server\HTTP\Middleware\Internal\CheckRequestBodySize;
use Rubix\Server\HTTP\Controllers\ModelController;
use Rubix\Server\HTTP\Controllers\DashboardController;
use Rubix\Server\HTTP\Controllers\StaticAssetsController;
use Rubix\Server\HTTP\Controllers\GraphQLController;
use Rubix\Server\GraphQL\Schema;
use Rubix\Server\Events\ShuttingDown;
use Rubix\Server\Listeners\UpdateDashboard;
use Rubix\Server\Listeners\LogFailures;
use Rubix\Server\Listeners\StopTimers;
use Rubix\Server\Listeners\CloseSSEChannels;
use Rubix\Server\Listeners\CloseSocket;
use Rubix\Server\Jobs\UpdateMemoryUsage;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\ML\Other\Loggers\BlackHole;
use GraphQL\Executor\Promise\Adapter\ReactPromiseAdapter;
use React\EventLoop\Factory as Loop;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\Http\Middleware\StreamingRequestMiddleware;
use React\Http\Middleware\LimitConcurrentRequestsMiddleware;
use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Server as HTTP;
use Psr\Log\LoggerInterface;

/**
 * HTTP Server
 *
 * A JSON over HTTP server exposing a Representational State Transfer (REST) API. The HTTP Server
 * operates with ubiquitous standards making it compatible with a wide range of systems. In addition,
 * it provides its own web-based user interface for real-time server monitoring.
 *
 * References:
 * [1] R. Fielding et al. (2014). Hypertext Transfer Protocol (HTTP/1.1): Semantics and Content.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class HTTPServer implements Server, Verbose
{
    protected const SERVER_NAME = 'Rubix ML HTTP Server';

    protected const MAX_TCP_PORT = 65535;

    protected const DASHBOARD_MEMORY_UPDATE_INTERVAL = 3.0;

    /**
     * The host address to bind the server to.
     *
     * @var string
     */
    protected $host;

    /**
     * The transmission control protocol (TCP) port to run the HTTP services on.
     *
     * @var int
     */
    protected $port;

    /**
     * The path to the certificate used to authenticate and encrypt the secure (HTTPS) channel.
     *
     * @var string|null
     */
    protected $cert;

    /**
     * The HTTP middleware stack.
     *
     * @var \Rubix\Server\HTTP\Middleware\Server\Middleware[]
     */
    protected $middlewares;

    /**
     * The maximum number of requests that can be handled concurrently.
     *
     * @var int
     */
    protected $maxConcurrentRequests;

    /**
     * The maximum number of events to store in the server-sent events (SSE) reconnect buffer.
     *
     * @var int
     */
    protected $sseReconnectBuffer;

    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * The event bus.
     *
     * @var \Rubix\Server\Services\EventBus
     */
    protected $eventBus;

    /**
     * The event loop.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * @param string $host
     * @param int $port
     * @param string|null $cert
     * @param \Rubix\Server\HTTP\Middleware\Server\Middleware[] $middlewares
     * @param int $maxConcurrentRequests
     * @param int $sseReconnectBuffer
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 8000,
        ?string $cert = null,
        array $middlewares = [],
        int $maxConcurrentRequests = 10,
        int $sseReconnectBuffer = 50
    ) {
        if (empty($host)) {
            throw new InvalidArgumentException('Host address cannot be empty.');
        }

        if ($port < 0 or $port > self::MAX_TCP_PORT) {
            throw new InvalidArgumentException('Port number must be'
                . ' between 0 and ' . self::MAX_TCP_PORT . ", $port given.");
        }

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof Middleware) {
                throw new InvalidArgumentException('Middleware must implement'
                    . ' the Middleware interface.');
            }
        }

        if ($maxConcurrentRequests < 1) {
            throw new InvalidArgumentException('Max concurrent requests must'
                . " be greater than 0, $maxConcurrentRequests given.");
        }

        if ($sseReconnectBuffer < 0) {
            throw new InvalidArgumentException('SSE retry buffer must'
                . " be greater than 0, $sseReconnectBuffer given.");
        }

        $this->host = $host;
        $this->port = $port;
        $this->cert = $cert;
        $this->middlewares = $middlewares;
        $this->maxConcurrentRequests = $maxConcurrentRequests;
        $this->sseReconnectBuffer = $sseReconnectBuffer;
        $this->logger = new BlackHole();
    }

    /**
     * Return the host address the server is bound to.
     *
     * @internal
     *
     * @return string
     */
    public function host() : string
    {
        return $this->host;
    }

    /**
     * Return the TCP port the server is providing HTTP service on.
     *
     * @internal
     *
     * @return int
     */
    public function port() : int
    {
        return $this->port;
    }

    /**
     * Return the maximum number of concurrent requests.
     *
     * @internal
     *
     * @return int
     */
    public function maxConcurrentRequests() : int
    {
        return $this->maxConcurrentRequests;
    }

    /**
     * Return the size of the SSE reconnect buffer.
     *
     * @internal
     *
     * @return int
     */
    public function sseReconnectBuffer() : int
    {
        return $this->sseReconnectBuffer;
    }

    /**
     * Sets a psr-3 logger.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }

    /**
     * Boot up the server.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function serve(Estimator $estimator) : void
    {
        $loop = Loop::create();

        $this->logger->info('HTTP Server booting up');

        $scheduler = new Scheduler($loop);

        $socket = new Socket("{$this->host}:{$this->port}", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $dashboardChannel = new SSEChannel($this->sseReconnectBuffer);

        $dashboard = new Dashboard($this, $dashboardChannel);

        $schema = new Schema($dashboard);

        $memoryTimer = $scheduler->repeat(
            self::DASHBOARD_MEMORY_UPDATE_INTERVAL,
            new UpdateMemoryUsage($dashboard->memory())
        );

        $eventBus = new EventBus(Subscriptions::subscribe([
            new UpdateDashboard($dashboard),
            new LogFailures($this->logger),
            new StopTimers($scheduler, [
                $memoryTimer,
            ]),
            new CloseSSEChannels([
                $dashboardChannel,
            ]),
            new CloseSocket($socket),
        ]), $scheduler, $this->logger);

        $router = new Router(Routes::collect([
            new DashboardController($dashboard, $dashboardChannel),
            new ModelController($estimator, $eventBus),
            new GraphQLController($schema, new ReactPromiseAdapter()),
            new StaticAssetsController(),
        ]));

        $postMaxSize = $dashboard->settings()->postMaxSize();

        $stack = [
            new StreamingRequestMiddleware(),
            new DispatchEvents($eventBus),
        ];

        $stack = array_merge($stack, $this->middlewares);

        $stack[] = new CheckRequestBodySize($postMaxSize);
        $stack[] = new AttachServerHeaders(self::SERVER_NAME);
        $stack[] = new LimitConcurrentRequestsMiddleware($this->maxConcurrentRequests);
        $stack[] = new RequestBodyBufferMiddleware($postMaxSize);
        $stack[] = [$router, 'dispatch'];

        $server = new HTTP($loop, ...$stack);

        $server->listen($socket);

        if (extension_loaded('pcntl')) {
            $loop->addSignal(SIGQUIT, [$this, 'shutdown']);
        }

        $this->eventBus = $eventBus;
        $this->loop = $loop;

        $this->logger->info("Listening at {$this->host}"
            . " on port {$this->port}");

        $loop->run();
    }

    /**
     * Shut down the server.
     *
     * @param int $signal
     */
    public function shutdown(int $signal) : void
    {
        $this->loop->removeSignal($signal, [$this, 'shutdown']);

        $this->logger->info('Server shutting down');

        $this->eventBus->dispatch(new ShuttingDown());
    }
}
