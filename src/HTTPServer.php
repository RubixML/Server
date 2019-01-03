<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as Parser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use Rubix\Server\Middleware\Middleware;
use Rubix\Server\Controllers\Proba;
use Rubix\Server\Controllers\Predict;
use React\Http\Server as ReactServer;
use React\Socket\Server as Socket;
use React\EventLoop\Factory as Loop;
use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

class HTTPServer implements Server, LoggerAwareInterface
{
    const DEFAULT_HEADERS = [
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
     * The controller dispatcher i.e the router.
     * 
     * @var Dispatcher
     */
    protected $router;

    /**
     * The middleware stack.
     * 
     * @var \Rubix\Server\Middleware\Middleware[]
     */
    protected $middleware;

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
     * @param  array  $routes
     * @param  array  $middlewares
     * @param  string  $host
     * @param  int  $port
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(array $routes, array $middlewares = [], string $host = '127.0.0.1', int $port = 8888)
    {
        $collector = new RouteCollector(new Parser(), new DataGenerator());

        foreach ($routes as $uri => $estimator) {
            if (!is_string($uri) or empty($uri)) {
                throw new InvalidArgumentException('URI must be a non empty'
                    . ' string ' . gettype($uri) . ' found.');
            }

            if (!$estimator instanceof Estimator) {
                throw new InvalidArgumentException('Route must point to'
                    . ' an Estimator instance, ' . get_class($estimator)
                    . ' found.');
            }

            $collector->addGroup($uri, function (RouteCollector $r) use ($estimator) {
                $r->addRoute('POST', '/predict', new Predict($estimator));

                if ($estimator instanceof Probabilistic) {
                    $r->addRoute('POST', '/proba', new Proba($estimator));
                }
            });
        }

        $middlewares = array_values($middlewares);

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof Middleware) {
                throw new InvalidArgumentException('Middleware must implement'
                . ' the middleware interface, ' . get_class($middleware)
                . ' found.');
            }
        }

        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " positive, $port given.");
        }

        $collector->addRoute('GET', '/status', function (Request $request, array $params) {
            $uptime = time() - $this->start;

            return new ReactResponse(200, self::DEFAULT_HEADERS, json_encode([
                'uptime' => $uptime,
                'requests' => $this->n,
                'requests_min' => $this->n / ($uptime / 60),
                'requests_sec' => $this->n / $uptime,
            ]));
        });

        $this->host = $host;
        $this->port = $port;
        $this->router = new Dispatcher($collector->getData());
        $this->middleware = $middlewares;
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
     * Boot up the server.
     * 
     * @return void
     */
    public function run() : void
    {
        $loop = Loop::create();

        $socket = new Socket("$this->host:$this->port", $loop);

        $stack = array_merge($this->middleware, [[$this, 'handle']]);

        $server = new ReactServer($stack);

        $server->listen($socket);

        if ($this->logger) $this->logger->info('Server running at'
            . " $this->host on port $this->port");

        $this->start = time();
        $this->n = 1;

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

            $ip = $server['REMOTE_ADDR'] ?? '0.0.0.0';
            
            $this->logger->info(self::ROUTER_STATUS[$status]
            . " $method $uri from $ip");
        }

        switch ($status) {
            case Dispatcher::NOT_FOUND:
                return new ReactResponse(404);

            case Dispatcher::METHOD_NOT_ALLOWED:
                return new ReactResponse(405);
        }

        $response = $controller($request, $params);

        $this->n++;

        return $response;
    }
}