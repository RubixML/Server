<?php

namespace Rubix\Server;

use Rubix\ML\Learner;
use Rubix\ML\Estimator;
use Rubix\Server\Services\Router;
use Rubix\Server\Services\CommandBus;
use Rubix\Server\Http\Controllers\CommandsController;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Traits\LoggerAware;
use React\Http\Server as HTTPServer;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\EventLoop\Factory as Loop;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerAwareInterface;

/**
 * RPC Server
 *
 * A fast Remote Procedure Call (RPC) over HTTP(S) server that responds to messages called
 * commands. Commands are serialized over the wire using one of numerous lightweight
 * encodings including JSON, Gzipped JSON, and Igbinary.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RPCServer implements Server, LoggerAwareInterface
{
    use LoggerAware;

    public const SERVER_NAME = 'Rubix RPC Server';

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
     * The router.
     *
     * @var \Rubix\Server\Services\Router
     */
    protected $router;

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

        $this->router = new Router([
            '/commands' => [
                'POST' => new CommandsController($bus, $this->serializer),
            ],
        ]);

        $loop = Loop::create();

        $socket = new Socket("{$this->host}:{$this->port}", $loop);

        if ($this->cert) {
            $socket = new SecureSocket($socket, $loop, [
                'local_cert' => $this->cert,
            ]);
        }

        $stack = $this->middlewares;

        $stack[] = [$this, 'addServerHeaders'];
        $stack[] = [$this->router, 'dispatch'];

        $server = new HTTPServer($loop, ...$stack);

        $server->listen($socket);

        if ($this->logger) {
            $this->logger->info('HTTP RPC Server running at'
                . " {$this->host} on port {$this->port}");
        }

        $loop->run();
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
}
