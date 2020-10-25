<?php

namespace Rubix\Server;

use Rubix\ML\Learner;
use Rubix\ML\Estimator;
use Rubix\Server\Http\Controllers\RPCController;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Traits\LoggerAware;
use React\Http\Server as HTTPServer;
use React\Http\Message\Response as ReactResponse;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\EventLoop\Factory as Loop;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LogLevel;
use InvalidArgumentException;

use const Rubix\Server\Http\NOT_FOUND;
use const Rubix\Server\Http\METHOD_NOT_ALLOWED;
use const Rubix\Server\Http\INTERNAL_SERVER_ERROR;

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
class RPCServer implements Server
{
    use LoggerAware;

    public const SERVER_NAME = 'Rubix RPC Server';

    public const HTTP_ENDPOINT = '/commands';

    public const HTTP_METHOD = 'POST';

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
    protected $middlewares;

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
     * @param \Rubix\Server\Http\Middleware\Middleware[] $middlewares
     * @param \Rubix\Server\Serializers\Serializer $serializer
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 8888,
        ?string $cert = null,
        array $middlewares = [],
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

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof Middleware) {
                throw new InvalidArgumentException('Class must implement'
                . ' the Middleware interface.');
            }
        }

        $this->host = $host;
        $this->port = $port;
        $this->cert = $cert;
        $this->middlewares = array_values($middlewares);
        $this->serializer = $serializer ?? new JSON();
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

        $bus = CommandBus::boot($estimator, $this);

        $this->controller = new RPCController($bus, $this->serializer);

        $loop = Loop::create();

        $socket = new Socket("$this->host:$this->port", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $stack = [
            function (Request $request, callable $next) {
                return $next($request)->withHeader('Server', self::SERVER_NAME);
            },
        ];

        $stack = array_merge($stack, $this->middlewares);

        $stack[] = [$this, 'handle'];

        $server = new HTTPServer($loop, ...$stack);

        $server->listen($socket);

        $this->start = time();
        $this->requests = 0;

        if ($this->logger) {
            $this->logger->info('HTTP RPC Server running at'
                . " {$this->host} on port {$this->port}");
        }

        $loop->run();
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request) : Response
    {
        $method = $request->getMethod();

        $uri = $request->getUri()->getPath();

        switch (true) {
            case $uri !== self::HTTP_ENDPOINT:
                $response = new ReactResponse(NOT_FOUND);

                break 1;

            case $method !== self::HTTP_METHOD:
                $response = new ReactResponse(METHOD_NOT_ALLOWED, [
                    'Allowed' => self::HTTP_METHOD,
                ]);

                break 1;

            default:
                $response = $this->controller->handle($request);
        }

        if ($this->logger) {
            $server = $request->getServerParams();

            $ip = $server['REMOTE_ADDR'] ?? '-';

            $version = 'HTTP/' . $request->getProtocolVersion();

            $status = $response->getStatusCode();

            $size = $response->getBody()->getSize();

            $headers = $request->getHeaders();

            $agent = $headers['User-Agent'][0] ?? '-';

            $record = "$ip '$method $uri $version' $status $size $agent";

            $level = $status === INTERNAL_SERVER_ERROR
                ? LogLevel::ERROR
                : LogLevel::INFO;

            $this->logger->log($level, $record);
        }

        ++$this->requests;

        return $response;
    }
}
