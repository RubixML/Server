<?php

namespace Rubix\Server;

use Rubix\ML\Learner;
use Rubix\ML\Estimator;
use Rubix\Server\Traits\LoggerAware;
use Rubix\Server\Http\Controllers\RPCController;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Exceptions\InvalidArgumentException;
use React\Http\Server as HTTPServer;
use React\Http\Message\Response as ReactResponse;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\EventLoop\Factory as Loop;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerAwareInterface;

use const Rubix\Server\Http\NOT_FOUND;
use const Rubix\Server\Http\METHOD_NOT_ALLOWED;

/**
 * RPC Server
 *
 * A lightweight Remote Procedure Call (RPC) server over HTTP and HTTPS that responds to
 * serialized messages called commands.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RPCServer implements Server, LoggerAwareInterface
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
     * @param string $host
     * @param int $port
     * @param string|null $cert
     * @param \Rubix\Server\Http\Middleware\Middleware[] $middlewares
     * @param \Rubix\Server\Serializers\Serializer $serializer
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
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

        $this->controller = new RPCController($bus, $this->serializer);

        $loop = Loop::create();

        $socket = new Socket("{$this->host}:{$this->port}", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $addServerHeaders = function (Request $request, callable $next) {
            return $next($request)->withHeader('Server', self::SERVER_NAME);
        };

        $stack = $this->middlewares;

        $stack[] = $addServerHeaders;
        $stack[] = [$this, 'handle'];

        $server = new HTTPServer($loop, ...$stack);

        $server->listen($socket);

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

        return $response;
    }
}
