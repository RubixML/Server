<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\Server\Http\Router;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Controllers\RESTController;
use Rubix\Server\Http\Controllers\PredictionsController;
use Rubix\Server\Http\Controllers\ProbabilitiesController;
use Rubix\Server\Http\Controllers\AnomalyScoresController;
use Rubix\Server\Http\Responses\BadRequest;
use Rubix\Server\Http\Responses\UnsupportedMediaType;
use Rubix\Server\Services\CommandBus;
use Rubix\Server\Services\EventBus;
use Rubix\Server\Payloads\ErrorPayload;
use Rubix\Server\Events\RequestReceived;
use Rubix\Server\Events\ResponseSent;
use Rubix\Server\Listeners\IncrementResponseCounter;
use Rubix\Server\Models\Dashboard;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Traits\LoggerAware;
use Rubix\Server\Helpers\JSON;
use React\Http\Server as HTTPServer;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\EventLoop\Factory as Loop;
use React\Promise\PromiseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
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

    public const SERVER_NAME = 'Rubix REST Server';

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
     * The path to the certificate used to authenticate and encrypt the
     * secure (HTTPS) communication channel.
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
     * The router.
     *
     * @var \Rubix\Server\Http\Router
     */
    protected $router;

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

        $dashboard = new Dashboard();

        $eventBus = new EventBus([
            ResponseSent::class => [
                new IncrementResponseCounter($dashboard),
            ],
        ]);

        $commandBus = CommandBus::boot($estimator, $eventBus, $this->logger);

        $this->router = $this->bootRouter($estimator, $commandBus, $dashboard);

        $loop = Loop::create();

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
        $stack[] = [$this->router, 'dispatch'];

        $server = new HTTPServer($loop, ...$stack);

        $server->listen($socket);

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
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function dispatchEvents(Request $request, callable $next)
    {
        $this->eventBus->dispatch(new RequestReceived($request));

        $response = $next($request);

        $this->eventBus->dispatch(new ResponseSent($response));

        return $response;
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
    public function parseRequestBody(Request $request, callable $next)
    {
        $contentType = $request->getHeaderLine('Content-Type');

        $acceptedContentType = RESTController::HEADERS['Content-Type'];

        if ($contentType !== $acceptedContentType) {
            return new UnsupportedMediaType($acceptedContentType);
        }

        try {
            $json = JSON::decode($request->getBody());

            $request = $request->withParsedBody($json);
        } catch (Exception $exception) {
            $payload = ErrorPayload::fromException($exception);

            $data = JSON::encode($payload->asArray());

            return new BadRequest(RESTController::HEADERS, $data);
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
    public function addServerHeader(Request $request, callable $next) : PromiseInterface
    {
        $promise = resolve($next($request));

        return $promise->then(function (Response $response) {
            return $response->withHeader('Server', self::SERVER_NAME);
        });
    }

    /**
     * Boot the RESTful router.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\Services\CommandBus $commandBus
     * @param \Rubix\Server\Models\Dashboard $dashboard
     * @return \Rubix\Server\Http\Router $router
     */
    protected function bootRouter(Estimator $estimator, CommandBus $commandBus, Dashboard $dashboard) : Router
    {
        $dashboardController = new DashboardController($dashboard);

        $routes = [
            '/model/predictions' => [
                'POST' => new PredictionsController($commandBus),
            ],
            '/server/dashboard' => [
                'GET' => new DashboardController($dashboard),
            ],
        ];

        if ($estimator instanceof Probabilistic) {
            $routes['/model/probabilities'] = [
                'POST' => new ProbabilitiesController($commandBus),
            ];
        }

        if ($estimator instanceof Ranking) {
            $routes['/model/anomaly_scores'] = [
                'POST' => new AnomalyScoresController($commandBus),
            ];
        }

        return new Router($routes);
    }
}
