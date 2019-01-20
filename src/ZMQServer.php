<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\Server\CommandBus;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Commands\Command;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\QueryModelHandler;
use Rubix\Server\Handlers\ServerStatusHandler;
use Rubix\Server\Responses\ErrorResponse;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Serializers\Json;
use Rubix\ML\Other\Helpers\Params;
use React\EventLoop\Factory as Loop;
use React\ZMQ\Context;
use Psr\Log\LoggerAwareInterface as LoggerAware;
use Psr\Log\LoggerInterface as Logger;
use InvalidArgumentException;
use RuntimeException;
use Exception;
use ZMQ;

/**
 * Zero MQ Server
 * 
 * Fast and lightweight background messaging server that doesn't require a
 * separate message broker.
 * 
 * > **Note**: This server requires the [ZeroMQ PHP extension](https://php.net/manual/en/book.zmq.php).
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ZMQServer implements Server, LoggerAware
{
    const PROTOCOLS = [
        'tcp', 'inproc', 'ipc', 'pgm', 'epgm',
    ];

    /**
     * The host address to bind the server to.
     * 
     * @var string
     */
    protected $host;

    /**
     * The network port to run the http services on.
     * 
     * @var int
     */
    protected $port;

    /**
     * The transport protocol used to send and recieve messages.
     * 
     * @var string
     */
    protected $protocol;

    /**
     * The serializer used to serialize/unserialize messages before
     * and after transit.
     * 
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * The command bus.
     * 
     * @var \Rubix\Server\CommandBus
     */
    protected $commandBus;

    /**
     * The Zero MQ socket.
     * 
     * @var \React\ZMQ\SocketWrapper
     */
    protected $server;

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
     * The time that the server went up.
     * 
     * @var int|null
     */
    protected $start;

    /**
     * @param  \Rubix\ML\Estimator  $estimator
     * @param  string  $host
     * @param  int  $port
     * @param  string  $protocol
     * @param  \Rubix\Server\Serializers\Serializer|null  $serializer
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return void
     */
    public function __construct(Estimator $estimator, string $host = '127.0.0.1', int $port = 5555,
                                string $protocol = 'tcp', ?Serializer $serializer = null)
    {
        if (!extension_loaded('zmq')) {
            throw new RuntimeException('Zero MQ extension is not loaded,'
                . ' check PHP configuration.');
        }

        if (empty($host)) {
            throw new InvalidArgumentException('Host cannot be empty.');
        }

        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " a positive integer, $port given.");
        }

        if (!in_array($protocol, self::PROTOCOLS)) {
            throw new InvalidArgumentException("'$protocol' is an invalid"
                . ' protocol, only allowed '
                . implode(', ', self::PROTOCOLS) . '.');
        }

        if (is_null($serializer)) {
            $serializer = new Json();
        }

        $commands = [
            QueryModel::class => new QueryModelHandler($estimator),
            Predict::class => new PredictHandler($estimator),
            ServerStatus::class => new ServerStatusHandler($this),
        ];

        if ($estimator instanceof Probabilistic) {
            $commands[Proba::class] = new ProbaHandler($estimator);
        }

        $this->commandBus = new CommandBus($commands);

        $this->host = $host;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->serializer = $serializer;
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

        $context = new Context($loop);

        $server = $context->getSocket(ZMQ::SOCKET_REP);

        $server->getWrappedSocket()
            ->bind("$this->protocol://$this->host:$this->port");

        $server->on('message', [$this, 'handle']);

        $this->server = $server;

        if ($this->logger) $this->logger->info('Server running at'
            . " $this->host on port $this->port using $this->protocol"
            . " protocol.");

        $this->requests = 0;
        $this->start = time();

        $loop->run();
    }

    /**
     * Handle a request.
     * 
     * @param  string  $message
     * @throws \RuntimeException
     * @return void
     */
    public function handle(string $message) : void
    {
        try {
            $command = $this->serializer->unserialize($message);

            if (!$command instanceof Command) {
                throw new RuntimeException('Command could not'
                    . ' be reconstituted.');
            }

            $response = $this->commandBus->dispatch($command);

            if ($this->logger) $this->logger->info('Handled '
                . Params::shortName($command));

            $this->requests++;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());
        }

        $message = $this->serializer->serialize($response);

        $this->server->send($message);
    }
}