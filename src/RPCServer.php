<?php

namespace Rubix\Server;

use Rubix\ML\Learner;
use Rubix\ML\Ranking;
use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\ProbaSample;
use Rubix\Server\Commands\Rank;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\PredictSampleHandler;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\ProbaSampleHandler;
use Rubix\Server\Handlers\RankHandler;
use Rubix\Server\Handlers\QueryModelHandler;
use Rubix\Server\Handlers\ServerStatusHandler;
use Rubix\Server\Http\Controllers\RPCController;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Serializers\Json;
use Rubix\Server\Serializers\Serializer;
use React\Http\Server as HTTPServer;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\EventLoop\Factory as Loop;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerAwareInterface as LoggerAware;
use Psr\Log\LoggerInterface as Logger;
use InvalidArgumentException;

/**
 * RPC Server
 *
 * A lightweight Remote Procedure Call (RPC) server over HTTP and HTTPS
 * that responds to serialized messages called commands.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RPCServer implements Server, LoggerAware
{
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
     * The message serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * The RPC controller.
     *
     * @var \Rubix\Server\Http\Controllers\Controller
     */
    protected $controller;

    /**
     * The logger instance.
     *
     * @var Logger|null
     */
    protected $logger;

    /**
     * The timestamp from when the server went up.
     *
     * @var int|null
     */
    protected $start;

    /**
     * The number of requests that have been handled during this
     * run of the server.
     *
     * @var int
     */
    protected $requests = 0;

    /**
     * @param string $host
     * @param int $port
     * @param string|null $cert
     * @param array $middleware
     * @param \Rubix\Server\Serializers\Serializer $serializer
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 8888,
        ?string $cert = null,
        array $middleware = [],
        ?Serializer $serializer = null
    ) {
        if (empty($host)) {
            throw new InvalidArgumentException('Host cannot be empty.');
        }

        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " a positive integer, $port given.");
        }

        if (isset($cert) and empty($cert)) {
            throw new InvalidArgumentException('Certificate cannot be'
                . ' empty.');
        }

        foreach ($middleware as $mw) {
            if (!$mw instanceof Middleware) {
                throw new InvalidArgumentException('Class must implement'
                . ' middleware interface, ' . get_class($mw) . ' given.');
            }
        }

        $this->host = $host;
        $this->port = $port;
        $this->cert = $cert;
        $this->middleware = array_values($middleware);
        $this->serializer = $serializer ?? new Json();
    }

    /**
     * Sets a psr-3 logger.
     *
     * @param Logger|null $logger
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
     * Serve a model.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @throws \InvalidArgumentException
     */
    public function serve(Estimator $estimator) : void
    {
        if ($estimator instanceof Learner) {
            if (!$estimator->trained()) {
                throw new InvalidArgumentException('Cannot serve'
                    . ' an untrained learner.');
            }
        }

        $bus = $this->bootCommandBus($estimator);

        $this->controller = new RPCController($bus, $this->serializer);

        $loop = Loop::create();

        $socket = new Socket("$this->host:$this->port", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $stack = $this->middleware;
        $stack[] = [$this, 'handle'];

        $server = new HTTPServer($stack);

        $server->listen($socket);

        if ($this->logger) {
            $this->logger->info('HTTP RPC Server running at'
                . " $this->host on port $this->port");
        }

        $this->start = time();
        $this->requests = 0;

        $loop->run();
    }

    /**
     * Boot up the command bus.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @return \Rubix\Server\CommandBus
     */
    protected function bootCommandBus(Estimator $estimator) : CommandBus
    {
        $commands = [
            QueryModel::class => new QueryModelHandler($estimator),
            ServerStatus::class => new ServerStatusHandler($this),
            Predict::class => new PredictHandler($estimator),
        ];

        if ($estimator instanceof Learner) {
            $commands[PredictSample::class] = new PredictSampleHandler($estimator);
        }

        if ($estimator instanceof Probabilistic) {
            $commands[Proba::class] = new ProbaHandler($estimator);
            $commands[ProbaSample::class] = new ProbaSampleHandler($estimator);
        }

        if ($estimator instanceof Ranking) {
            $commands[Rank::class] = new RankHandler($estimator);
        }

        return new CommandBus($commands);
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request) : Response
    {
        $response = $this->controller->handle($request);

        if ($this->logger) {
            $method = $request->getMethod();
            $uri = $request->getUri()->getPath();

            $status = (string) $response->getStatusCode();

            $server = $request->getServerParams();

            $ip = $server['HTTP_CLIENT_IP'] ?? $server['REMOTE_ADDR'] ?? 'unknown';
            
            $this->logger->info("$status $method $uri from $ip");
        }

        return $response;
    }
}
