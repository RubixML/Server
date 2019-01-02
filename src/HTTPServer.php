<?php

namespace Rubix\Server;

use Rubix\ML\Wrapper;
use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\Server\Controllers\Proba;
use Rubix\Server\Controllers\Predict;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as Parser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use React\Http\Server as ReactServer;
use React\Socket\Server as Socket;
use React\EventLoop\Factory as Loop;
use Psr\Http\Message\ServerRequestInterface as Request;
use InvalidArgumentException;

class HTTPServer implements Server
{
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
     * @param  array  $routes
     * @param  string  $host
     * @param  int  $port
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(array $routes, string $host = '127.0.0.1', int $port = 8888)
    {
        $collector = new RouteCollector(new Parser(), new DataGenerator());

        foreach ($routes as $uri => $estimator) {
            if (!is_string($uri) or empty($uri)) {
                throw new InvalidArgumentException('URI must be a non empty'
                    . ' string ' . gettype($uri) . ' found.');
            }

            if (!$estimator instanceof Estimator) {
                throw new InvalidArgumentException('Route must point to'
                    . ' an Estimator instance, ' . gettype($estimator)
                    . ' found.');
            }

            $collector->addGroup($uri, function (RouteCollector $r) use ($estimator) {
                $r->addRoute('POST', '/predict', new Predict($estimator));

                if ($estimator instanceof Probabilistic) {
                    $r->addRoute('POST', '/proba', new Proba($estimator));
                }
            });
        }

        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " positive, $port given.");
        }

        $this->host = $host;
        $this->port = $port;
        $this->router = new Dispatcher($collector->getData());
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

        $server = new ReactServer(function (Request $request) {
            $uri = $request->getUri()->getPath();
            $method = $request->getMethod();

            list($status, $controller, $params) = $this->router->dispatch($method, $uri);

            return $controller->handle($request, $params);
        });

        $server->listen($socket);

        $loop->run();
    }
}