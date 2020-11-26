<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\Server\Models\Dashboard;
use Rubix\Server\Services\Bindings;
use Rubix\Server\Services\QueryBus;
use Rubix\Server\Services\Subscriptions;
use Rubix\Server\Services\EventBus;
use Rubix\Server\Services\Router;
use Rubix\Server\Services\Routes;
use Rubix\Server\Services\Scheduler;
use Rubix\Server\Services\SSEChannel;
use Rubix\Server\HTTP\Middleware\Middleware;
use Rubix\Server\HTTP\Controllers\ModelController;
use Rubix\Server\HTTP\Controllers\DashboardController;
use Rubix\Server\HTTP\Controllers\StaticAssetsController;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\ScoreHandler;
use Rubix\Server\Handlers\DashboardHandler;
use Rubix\Server\Events\RequestReceived;
use Rubix\Server\Events\ResponseSent;
use Rubix\Server\Events\ShuttingDown;
use Rubix\Server\Listeners\UpdateDashboard;
use Rubix\Server\Listeners\LogFailures;
use Rubix\Server\Listeners\StopTimers;
use Rubix\Server\Listeners\CloseSSEChannels;
use Rubix\Server\Listeners\CloseSocket;
use Rubix\Server\Specifications\LearnerIsTrained;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Traits\LoggerAware;
use Rubix\ML\Other\Loggers\BlackHole;
use React\EventLoop\Factory as Loop;
use React\Http\Server as HTTP;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\Filesystem\Filesystem;
use React\Promise\PromiseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use function React\Promise\resolve;

/**
 * HTTP Server
 *
 * A JSON over HTTP server exposing a Representational State Transfer (REST) API. The HTTP Server
 * operates with ubiquitous standards making it compatible with a wide range of systems. In addition,
 * it provides its own web-based user interface for real-time server monitoring.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class HTTPServer implements Server, Verbose
{
    use LoggerAware;

    protected const SERVER_NAME = 'Rubix ML HTTP Server/' . VERSION;

    protected const MAX_TCP_PORT = 65535;

    protected const MEMORY_UPDATE_INTERVAL = 1.0;

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
     * @var \Rubix\Server\HTTP\Middleware\Middleware[]
     */
    protected $middlewares;

    /**
     * The size of the server-sent events retry buffer.
     *
     * @var int
     */
    protected $sseRetryBuffer;

    /**
     * The event loop.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * The network socket.
     *
     * @var \React\Socket\ServerInterface
     */
    protected $socket;

    /**
     * The event bus.
     *
     * @var \Rubix\Server\Services\EventBus
     */
    protected $eventBus;

    /**
     * @param string $host
     * @param int $port
     * @param string|null $cert
     * @param mixed[] $middlewares
     * @param int $sseRetryBuffer
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 80,
        ?string $cert = null,
        array $middlewares = [],
        int $sseRetryBuffer = 50
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

        if ($sseRetryBuffer < 0) {
            throw new InvalidArgumentException('SSE retry buffer must'
                . " be greater than 0, $sseRetryBuffer given.");
        }

        $this->host = $host;
        $this->port = $port;
        $this->cert = $cert;
        $this->middlewares = $middlewares;
        $this->sseRetryBuffer = $sseRetryBuffer;
        $this->logger = new BlackHole();
    }

    /**
     * Boot up the server.
     *
     * @param \Rubix\ML\Estimator $estimator
     */
    public function serve(Estimator $estimator) : void
    {
        if ($estimator instanceof Learner) {
            LearnerIsTrained::with($estimator)->check();
        }

        $this->logger->info('HTTP Server booting up');

        $loop = Loop::create();

        $scheduler = new Scheduler($loop);

        $socket = new Socket("{$this->host}:{$this->port}", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $filesystem = Filesystem::create($loop);

        $dashboardChannel = new SSEChannel($this->sseRetryBuffer);

        $dashboard = new Dashboard($dashboardChannel);

        $memoryTimer = $scheduler->repeat(
            self::MEMORY_UPDATE_INTERVAL,
            [$dashboard->memory(), 'updateUsage']
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

        $queryBus = new QueryBus(Bindings::bind([
            new PredictHandler($estimator),
            $estimator instanceof Probabilistic ? new ProbaHandler($estimator) : null,
            $estimator instanceof Ranking ? new ScoreHandler($estimator) : null,
            new DashboardHandler($dashboard),
        ]), $eventBus);

        $router = new Router(Routes::collect([
            new ModelController($queryBus),
            new DashboardController($queryBus, $dashboardChannel),
            new StaticAssetsController($filesystem),
        ]));

        $stack = [];

        $stack[] = [$this, 'dispatchEvents'];

        $stack = array_merge($stack, $this->middlewares);

        $stack[] = [$this, 'addServerHeader'];
        $stack[] = [$router, 'dispatch'];

        $server = new HTTP($loop, ...$stack);

        $server->listen($socket);

        $this->logger->info("Listening at {$this->host}"
            . " on port {$this->port}");

        $this->eventBus = $eventBus;

        $shutdown = function (int $signal) use ($loop, &$shutdown) {
            $loop->removeSignal($signal, $shutdown);

            $this->logger->info('Shutting down');

            $this->eventBus->dispatch(new ShuttingDown($this));
        };

        $loop->addSignal(SIGTERM, $shutdown);

        $loop->run();
    }

    /**
     * Dispatch events related to the request/response cycle.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \React\Promise\PromiseInterface
     */
    public function dispatchEvents(ServerRequestInterface $request, callable $next) : PromiseInterface
    {
        $this->eventBus->dispatch(new RequestReceived($request));

        return resolve($next($request))->then(function (ResponseInterface $response) : ResponseInterface {
            $this->eventBus->dispatch(new ResponseSent($response));

            return $response;
        });
    }

    /**
     * Add the HTTP server header.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \React\Promise\PromiseInterface
     */
    public function addServerHeader(ServerRequestInterface $request, callable $next) : PromiseInterface
    {
        return resolve($next($request))->then(function (ResponseInterface $response) : ResponseInterface {
            return $response->withHeader('Server', self::SERVER_NAME);
        });
    }
}
