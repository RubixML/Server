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
use Rubix\Server\Services\SSEChannel;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Controllers\ModelController;
use Rubix\Server\Http\Controllers\QueriesController;
use Rubix\Server\Http\Controllers\DashboardController;
use Rubix\Server\Http\Controllers\StaticAssetsController;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\ScoreHandler;
use Rubix\Server\Handlers\DashboardHandler;
use Rubix\Server\Events\ResponseSent;
use Rubix\Server\Listeners\UpdateDashboard;
use Rubix\Server\Listeners\LogFailures;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
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
 * A JSON over HTTP(S) server exposing a REST (Representational State Transfer) API. The REST
 * server exposes one endpoint (resource) per command and can be queried using any standard
 * HTTP client.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class HTTPServer implements Server, Verbose
{
    use LoggerAware;

    public const SERVER_NAME = 'Rubix ML REST Server';

    protected const MAX_TCP_PORT = 65535;

    /**
     * The host address to bind the server to.
     *
     * @var string
     */
    protected $host;

    /**
     * The network port to run the HTTP services on.
     *
     * @var int
     */
    protected $port;

    /**
     * The path to the certificate used to authenticate and encrypt the secure (HTTPS)
     * communication channel.
     *
     * @var string|null
     */
    protected $cert;

    /**
     * The HTTP middleware stack.
     *
     * @var \Rubix\Server\Http\Middleware\Middleware[]
     */
    protected $middlewares;

    /**
     * The message serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

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
     * The SSE channels.
     *
     * @var \Rubix\Server\Services\SSEChannel[]
     */
    protected $channels = [
        //
    ];

    /**
     * @param string $host
     * @param int $port
     * @param string|null $cert
     * @param mixed[] $middlewares
     * @param \Rubix\Server\Serializers\Serializer $serializer
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 8080,
        ?string $cert = null,
        array $middlewares = [],
        ?Serializer $serializer = null
    ) {
        if (empty($host)) {
            throw new InvalidArgumentException('Host cannot be empty.');
        }

        if ($port < 0 or $port > self::MAX_TCP_PORT) {
            throw new InvalidArgumentException('Port number must be'
                . ' between 0 and ' . self::MAX_TCP_PORT . ", $port given.");
        }

        if (isset($cert) and empty($cert)) {
            throw new InvalidArgumentException('Certificate cannot be empty.');
        }

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof Middleware) {
                throw new InvalidArgumentException('Class must implement'
                    . ' middleware interface.');
            }
        }

        $this->host = $host;
        $this->port = $port;
        $this->cert = $cert;
        $this->middlewares = array_values($middlewares);
        $this->serializer = $serializer ?? new JSON();
        $this->logger = new BlackHole();
    }

    /**
     * Serve a model.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function serve(Estimator $estimator) : void
    {
        if ($estimator instanceof Learner) {
            if (!$estimator->trained()) {
                throw new InvalidArgumentException('Cannot serve'
                    . ' an untrained learner.');
            }
        }

        $loop = Loop::create();

        $socket = new Socket("{$this->host}:{$this->port}", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $filesystem = Filesystem::create($loop);

        $dashboardChannel = new SSEChannel(50);

        $dashboard = new Dashboard($dashboardChannel);

        $eventBus = new EventBus(Subscriptions::subscribe([
            new UpdateDashboard($dashboard),
            new LogFailures($this->logger),
        ]), $loop, $this->logger);

        $queryBus = new QueryBus(Bindings::bind([
            new PredictHandler($estimator),
            new DashboardHandler($dashboard),
            $estimator instanceof Probabilistic ? new ProbaHandler($estimator) : null,
            $estimator instanceof Ranking ? new ScoreHandler($estimator) : null,
        ]), $eventBus);

        $router = new Router(Routes::collect([
            new ModelController($queryBus),
            new QueriesController($queryBus, $this->serializer),
            new DashboardController($queryBus, $dashboardChannel),
            new StaticAssetsController($filesystem),
        ]));

        $stack = [];

        $stack[] = [$this, 'dispatchEvents'];

        $stack = array_merge($stack, $this->middlewares);

        $stack[] = [$this, 'addServerHeader'];
        $stack[] = [$router, 'dispatch'];

        $server = new HTTP($loop, ...$stack);

        $this->loop = $loop;
        $this->socket = $socket;
        $this->eventBus = $eventBus;

        $this->channels = [
            $dashboardChannel,
        ];

        $loop->addSignal(SIGINT, [$this, 'shutdown']);

        $server->listen($socket);

        $this->logger->info('HTTP REST Server running at'
            . " {$this->host} on port {$this->port}");

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

    /**
     * Shut down the server.
     *
     * @internal
     *
     * @param int $signal
     */
    public function shutdown(int $signal) : void
    {
        $this->loop->removeSignal($signal, [$this, 'shutdown']);

        $this->logger->info('Server shutting down');

        foreach ($this->channels as $channel) {
            $channel->close();
        }

        $this->socket->close();
    }
}
