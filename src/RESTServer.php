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
use Rubix\Server\Http\Router;
use Rubix\Server\Http\Routes;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Controllers\ModelController;
use Rubix\Server\Http\Controllers\StaticAssetsController;
use Rubix\Server\Http\Controllers\DashboardController;
use Rubix\Server\Http\Controllers\RESTController;
use Rubix\Server\Http\Responses\BadRequest;
use Rubix\Server\Http\Responses\UnsupportedMediaType;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\ScoreHandler;
use Rubix\Server\Handlers\DashboardHandler;
use Rubix\Server\Events\RequestReceived;
use Rubix\Server\Events\ResponseSent;
use Rubix\Server\Listeners\UpdateDashboard;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Traits\LoggerAware;
use Rubix\Server\Helpers\JSON;
use React\EventLoop\Factory as Loop;
use React\Http\Server as HTTPServer;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\Filesystem\Filesystem;
use React\Promise\PromiseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Exception;

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
class RESTServer implements Server, LoggerAwareInterface
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
     * The event bus.
     *
     * @var \Rubix\Server\Services\EventBus
     */
    protected $eventBus;

    /**
     * The socket.
     *
     * @var \React\Socket\ServerInterface
     */
    protected $socket;

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
     * @param mixed[] $middlewares
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 8080,
        ?string $cert = null,
        array $middlewares = []
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

        $dashboard = new Dashboard();

        $filesystem = Filesystem::create($loop);

        $eventBus = new EventBus(Subscriptions::subscribe([
            new UpdateDashboard($dashboard),
        ]), $loop, $this->logger);

        $handlers = [
            new PredictHandler($estimator),
            new DashboardHandler($dashboard),
        ];

        if ($estimator instanceof Probabilistic) {
            $handlers[] = new ProbaHandler($estimator);
        }

        if ($estimator instanceof Ranking) {
            $handlers[] = new ScoreHandler($estimator);
        }

        $queryBus = new QueryBus(Bindings::bind($handlers), $eventBus, $this->logger);

        $router = new Router(Routes::collect([
            new ModelController($queryBus),
            new DashboardController($queryBus),
            new StaticAssetsController($filesystem),
        ]));

        $socket = new Socket("{$this->host}:{$this->port}", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $stack = [];

        $stack[] = [$this, 'dispatchEvents'];

        $stack = array_merge($stack, $this->middlewares);

        $stack[] = [$this, 'parseRequestBody'];
        $stack[] = [$this, 'addServerHeader'];
        $stack[] = [$router, 'dispatch'];

        $server = new HTTPServer($loop, ...$stack);

        $server->listen($socket);

        $loop->addSignal(SIGINT, [$this, 'shutdown']);

        $this->eventBus = $eventBus;
        $this->socket = $socket;
        $this->loop = $loop;

        if ($this->logger) {
            $this->logger->info('HTTP REST Server running at'
                . " {$this->host} on port {$this->port}");
        }

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
     * Parse the request body content.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function parseRequestBody(ServerRequestInterface $request, callable $next)
    {
        $stream = $request->getBody();

        if ($stream->getSize()) {
            $contentType = $request->getHeaderLine('Content-Type');

            $acceptedContentType = RESTController::HEADERS['Accept'];

            if ($contentType !== $acceptedContentType) {
                return new UnsupportedMediaType($acceptedContentType);
            }

            try {
                $json = JSON::decode($stream);
            } catch (Exception $exception) {
                return new BadRequest(RESTController::HEADERS, JSON::encode([
                    'message' => $exception->getMessage(),
                ]));
            }

            $request = $request->withParsedBody($json);
        }

        return $next($request);
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

        $this->socket->close();

        if ($this->logger) {
            $this->logger->info('Server shutting down');
        }
    }
}
