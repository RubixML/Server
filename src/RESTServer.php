<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\Server\CommandBus;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\QueryModelHandler;
use Rubix\Server\Handlers\ServerStatusHandler;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Controllers\PredictionsController;
use Rubix\Server\Http\Controllers\ProbabilitiesController;
use Rubix\Server\Http\Controllers\QueryModelController;
use Rubix\Server\Http\Controllers\ServerStatusController;
use FastRoute\RouteCollector as Collector;
use FastRoute\RouteParser\Std as Parser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use React\Http\Server as ReactServer;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\EventLoop\Factory as Loop;
use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerAwareInterface as LoggerAware;
use Psr\Log\LoggerInterface as Logger;
use InvalidArgumentException;

/**
 * REST Server
 *
 * Representational State Transfer (REST) server over HTTP and HTTPS where
 * each model (*resource*) is given a unique user-specified URI prefix.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RESTServer implements Server, LoggerAware
{
    const MODEL_PREFIX = '/model';
    const SERVER_PREFIX = '/server';

    const PREDICT_ENDPOINT = '/predictions';
    const PROBA_ENDPOINT = '/probabilities';
    const SERVER_STATUS_ENDPOINT = '/status';

    const ROUTER_STATUS = [
        0 => '404',
        1 => '200',
        2 => '405',
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
    protected $middleware;

    /**
     * The controller dispatcher i.e the router.
     * 
     * @var Dispatcher
     */
    protected $router;

    /**
     * The logger instance.
     *
     * @var Logger|null
     */
    protected $logger;

    /**
     * The number of requests that have been handled during this
     * run of the server.
     * 
     * @var int
     */
    protected $requests;

    /**
     * The timestamp from when the server went up.
     * 
     * @var int|null
     */
    protected $start;

    /**
     * @param  \Rubix\ML\Estimator  $estimator
     * @param  string  $host
     * @param  int  $port
     * @param  array  $middleware
     * @param  string|null  $cert
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(Estimator $estimator, string $host = '127.0.0.1', int $port = 8888,
                                array $middleware = [], ?string $cert = null)
    {
        if (empty($host)) {
            throw new InvalidArgumentException('Host cannot be empty.');
        }

        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " a positive integer, $port given.");
        }

        foreach ($middleware as $mw) {
            if (!$mw instanceof Middleware) {
                throw new InvalidArgumentException('Class must implement'
                . ' the middleware interface, ' . get_class($mw)
                . ' given.');
            }
        }

        if (isset($cert) and empty($cert)) {
            throw new InvalidArgumentException('Certificate cannot be'
                . ' empty.');
        }

        $commands = [
            QueryModel::class => new QueryModelHandler($estimator),
            Predict::class => new PredictHandler($estimator),
            ServerStatus::class => new ServerStatusHandler($this),
        ];

        if ($estimator instanceof Probabilistic) {
            $commands[Proba::class] = new ProbaHandler($estimator);
        }

        $commandBus = new CommandBus($commands);

        $collector = new Collector(new Parser(), new DataGenerator());

        $collector->get(self::MODEL_PREFIX, new QueryModelController($commandBus));

        $collector->addGroup(self::MODEL_PREFIX, function (Collector $group) use ($commandBus, $estimator) {
            $group->post(self::PREDICT_ENDPOINT, new PredictionsController($commandBus));
            
            if ($estimator instanceof Probabilistic) {
                $group->post(self::PROBA_ENDPOINT, new ProbabilitiesController($commandBus));
            }
        });

        $collector->addGroup(self::SERVER_PREFIX, function (Collector $group) use ($commandBus) {
            $group->get(self::SERVER_STATUS_ENDPOINT, new ServerStatusController($commandBus));
        });

        $this->router = new Dispatcher($collector->getData());
        $this->middleware = array_values($middleware);

        $this->host = $host;
        $this->port = $port;
        $this->cert = $cert;
        $this->requests = 0;
    }

    /**
     * Sets a logger.
     *
     * @param Logger|null  $logger
     * @return void
     */
    public function setLogger(?Logger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Return the number of requests that have been received.
     * 
     * @var int
     */
    public function requests() : int
    {
        return $this->requests;
    }

    /**
     * Return the uptime of the server in seconds.
     * 
     * @return int
     */
    public function uptime() : int
    {
        return $this->start ? (time() - $this->start) ?: 1 : 0;
    }

    /**
     * Boot up the server.
     * 
     * @return void
     */
    public function run() : void
    {
        $loop = Loop::create();

        $socket = new Socket("$this->host:$this->port", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $stack = array_merge($this->middleware, [[$this, 'handle']]);

        $server = new ReactServer($stack);

        $server->listen($socket);

        if ($this->logger) $this->logger->info('Server running at'
            . " $this->host on port $this->port");

        $this->requests = 0;
        $this->start = time();

        $loop->run();
    }

    /**
     * Handle an incoming request.
     * 
     * @param  Request  $request
     * @return Response
     */
    public function handle(Request $request) : Response
    {
        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();

        $route = $this->router->dispatch($method, $uri);

        list($status, $controller, $params) = array_pad($route, 3, null);

        if ($this->logger) {
            $server = $request->getServerParams();

            $ip = $server['REMOTE_ADDR'] ?? 'unknown';
            
            $this->logger->info(self::ROUTER_STATUS[$status]
                . " $method $uri from $ip");
        }

        switch ($status) {
            case Dispatcher::FOUND:
                $response = $controller->handle($request, $params);

                $this->requests++;
                
                return $response;

            case Dispatcher::NOT_FOUND:
                return new ReactResponse(404);

            case Dispatcher::METHOD_NOT_ALLOWED:
                return new ReactResponse(405);

            default:
                return new ReactResponse(500);
        }
    }
}