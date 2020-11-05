<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\Server\Traits\LoggerAware;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Controllers\PredictionsController;
use Rubix\Server\Http\Controllers\SamplePredictionController;
use Rubix\Server\Http\Controllers\ProbabilitiesController;
use Rubix\Server\Http\Controllers\SampleProbabilitiesController;
use Rubix\Server\Http\Controllers\ScoresController;
use Rubix\Server\Http\Controllers\SampleScoreController;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Server as HTTPServer;
use React\Http\Message\Response as ReactResponse;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\EventLoop\Factory as Loop;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use FastRoute\Dispatcher;
use Psr\Log\LoggerAwareInterface;

use const Rubix\Server\Http\NOT_FOUND;
use const Rubix\Server\Http\METHOD_NOT_ALLOWED;

/**
 * HTTP Server
 *
 * A standalone JSON over HTTP and secure HTTP server exposing a REST (Representational State
 * Transfer) API.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RESTServer implements Server, LoggerAwareInterface
{
    use LoggerAware;

    public const SERVER_NAME = 'Rubix REST Server';

    public const ENDPOINTS = [
        'predict' => '/model/predictions',
        'predict_sample' => '/model/predictions/sample',
        'proba' => '/model/probabilities',
        'proba_sample' => '/model/probabilities/sample',
        'score' => '/model/scores',
        'score_sample' => '/model/scores/sample',
    ];

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
     * The controller dispatcher i.e the router.
     *
     * @var \FastRoute\Dispatcher
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

        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " a positive integer, $port given.");
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

        $bus = CommandBus::boot($estimator, $this->logger);

        $this->router = $this->bootRouter($estimator, $bus);

        $loop = Loop::create();

        $socket = new Socket("{$this->host}:{$this->port}", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $stack = $this->middlewares;

        $stack[] = [$this, 'addServerHeaders'];
        $stack[] = [$this, 'handle'];

        $server = new HTTPServer($loop, ...$stack);

        $server->listen($socket);

        if ($this->logger) {
            $this->logger->info('HTTP REST Server running at'
                . " {$this->host} on port {$this->port}");
        }

        $loop->run();
    }

    /**
     * Handle an incoming request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request) : Response
    {
        $method = $request->getMethod();

        $path = $request->getUri()->getPath();

        $route = $this->router->dispatch($method, $path);

        [$status, $controller, $params] = array_pad($route, 3, null);

        switch ($status) {
            case Dispatcher::NOT_FOUND:
                $response = new ReactResponse(NOT_FOUND);

                break 1;

            case Dispatcher::METHOD_NOT_ALLOWED:
                /** @var string[] $allowed */
                $allowed = $controller;

                $response = new ReactResponse(METHOD_NOT_ALLOWED, [
                    'Allowed' => implode(', ', $allowed),
                ]);

                break 1;

            default:
                $response = $controller->handle($request, $params);
        }

        return $response;
    }

    /**
     * Add the HTTP headers specific to this server.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function addServerHeaders(Request $request, callable $next) : Response
    {
        return $next($request)->withHeader('Server', self::SERVER_NAME);
    }

    /**
     * Boot up the RESTful router.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\CommandBus $bus
     * @return \FastRoute\Dispatcher
     */
    protected function bootRouter(Estimator $estimator, CommandBus $bus) : Dispatcher
    {
        $collector = new RouteCollector(new Std(), new GroupCountBasedDataGenerator());

        $collector->post(
            self::ENDPOINTS['predict'],
            new PredictionsController($bus)
        );

        if ($estimator instanceof Learner) {
            $collector->post(
                self::ENDPOINTS['predict_sample'],
                new SamplePredictionController($bus)
            );
        }

        if ($estimator instanceof Probabilistic) {
            $collector->post(
                self::ENDPOINTS['proba'],
                new ProbabilitiesController($bus)
            );

            $collector->post(
                self::ENDPOINTS['proba_sample'],
                new SampleProbabilitiesController($bus)
            );
        }

        if ($estimator instanceof Ranking) {
            $collector->post(
                self::ENDPOINTS['score'],
                new ScoresController($bus)
            );

            $collector->post(
                self::ENDPOINTS['score_sample'],
                new SampleScoreController($bus)
            );
        }

        return new GroupCountBasedDispatcher($collector->getData());
    }
}
