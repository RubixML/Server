<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\Server\Middleware\Middleware;
use Rubix\Server\Controllers\Status;
use Rubix\Server\Controllers\Prediction;
use Rubix\Server\Controllers\Probabilities;
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
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

class RESTServer implements Server, LoggerAwareInterface
{
    const PREDICTION_ENDPOINT = '/predictions';
    const PROBA_ENDPOINT = '/probabilities';
    const STATUS_ENDPOINT = '/status';

    const HEADERS = [
        'Content-Type' => 'text/json',
    ];

    const ROUTER_STATUS = [
        0 => '404',
        1 => '200',
        2 => '405',
    ];

    /**
     * The host to bind the server to.
     * 
     * @var string
     */
    protected $host;

    /**
     * The port to run the http services on.
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
     * The middleware stack.
     * 
     * @var \Rubix\Server\Middleware\Middleware[]
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
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * The time that the server went up.
     * 
     * @var float|null
     */
    protected $start;

    /**
     * The number of requests that have been handled during this
     * run.
     * 
     * @var int
     */
    protected $n;

    /**
     * @param  array  $mapping
     * @param  array  $middleware
     * @param  string  $host
     * @param  int  $port
     * @param  string|null  $cert
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(array $mapping, array $middleware = [], string $host = '127.0.0.1',
                                int $port = 8888, ?string $cert = null)
    {
        $this->registerRoutes($mapping);

        foreach ($middleware as $mw) {
            if (!$mw instanceof Middleware) {
                throw new InvalidArgumentException('Middleware must implement'
                . ' the middleware interface, ' . get_class($mw) . ' found.');
            }
        }

        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " positive, $port given.");
        }

        if (isset($cert) and empty($cert)) {
            throw new InvalidArgumentException('Certificate cannot be'
                . ' empty.');
        }

        $this->middleware = array_values($middleware);

        $this->host = $host;
        $this->port = $port;
        $this->cert = $cert;
    }

    /**
     * Sets a logger.
     *
     * @param \Psr\Log\LoggerInterface|null  $logger
     * @return void
     */
    public function setLogger(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Return the number of request served.
     * 
     * @var int
     */
    public function requests() : int
    {
        return $this->n;
    }

    /**
     * Return the uptime of the server.
     * 
     * @return int
     */
    public function uptime() : int
    {
        return (int) (time() - $this->start) ?: 1;
    }

    /**
     * Register the routes for the server.
     * 
     * @param  array  $mapping
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function registerRoutes(array $mapping) : void
    {
        $collector = new Collector(new Parser(), new DataGenerator());

        foreach ($mapping as $prefix => $estimator) {
            if (!is_string($prefix) or empty($prefix)) {
                throw new InvalidArgumentException('Prefix must be a non'
                    . ' empty string ' . gettype($prefix) . ' found.');
            }

            if (!$estimator instanceof Estimator) {
                throw new InvalidArgumentException('Route must point to'
                    . ' an Estimator instance, ' . get_class($estimator)
                    . ' found.');
            }

            $collector->addGroup($prefix, function (Collector $r) use ($estimator) {
                $r->addRoute('POST', self::PREDICTION_ENDPOINT, new Prediction($estimator));

                if ($estimator instanceof Probabilistic) {
                    $r->addRoute('POST', self::PROBA_ENDPOINT, new Probabilities($estimator));
                }
            });
        }

        $collector->addRoute('GET', self::STATUS_ENDPOINT, new Status($this));

        $router = new Dispatcher($collector->getData());

        $this->router = $router;
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

        $this->start = time();
        $this->n = 0;

        if ($this->logger) $this->logger->info('Server running at'
            . " $this->host on port $this->port");

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

        list($status, $controller, $params) = $this->router->dispatch($method, $uri);

        if ($this->logger) {
            $server = $request->getServerParams();

            $ip = $server['REMOTE_ADDR'] ?? 'unknown';
            
            $this->logger->info(self::ROUTER_STATUS[$status]
            . " $method $uri from $ip");
        }

        $this->n++;

        switch ($status) {
            case Dispatcher::FOUND:
                return $controller($request, $params);

            case Dispatcher::NOT_FOUND:
                return new ReactResponse(404);

            case Dispatcher::METHOD_NOT_ALLOWED:
                return new ReactResponse(405);

            default:
                return new ReactResponse(500);
        }
    }
}