<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use Rubix\Server\Responses\Response;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Igbinary;
use Rubix\Server\Serializers\Native;
use Rubix\Server\Serializers\Serializer;
use GuzzleHttp\Client as Guzzle;
use InvalidArgumentException;
use RuntimeException;
use Exception;

use function get_class;

/**
 * RPC Client
 *
 * The RPC Client is made to communicate with a RPC Server over HTTP or Secure
 * HTTP (HTTPS). In the event of a network failure, it uses a backoff and retry
 * mechanism as a failover strategy.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RPCClient implements Client
{
    public const MAX_DELAY = 5000000;

    protected const SERIALIZER_HEADERS = [
        JSON::class => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        Native::class => [
            'Content-Type' => 'application/octet-stream',
            'Accept' => 'application/octet-stream',
        ],
        Igbinary::class => [
            'Content-Type' => 'application/octet-stream',
            'Accept' => 'application/octet-stream',
        ],
    ];

    /**
     * The Guzzle client.
     *
     * @var Guzzle
     */
    protected $client;

    /**
     * The number of seconds to wait before retrying.
     *
     * @var float
     */
    protected $timeout;

    /**
     * The number of retries before giving up.
     *
     * @var int
     */
    protected $retries;

    /**
     * The initial delay between request retries.
     *
     * @var int
     */
    protected $delay;

    /**
     * The serializer used to serialize/unserialize messages before
     * and after transit.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * @param string $host
     * @param int $port
     * @param bool $secure
     * @param mixed[] $headers
     * @param float $timeout
     * @param int $retries
     * @param float $delay
     * @param \Rubix\Server\Serializers\Serializer|null $serializer
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 8888,
        bool $secure = false,
        array $headers = [],
        ?Serializer $serializer = null,
        float $timeout = 0.0,
        int $retries = 2,
        float $delay = 0.3
    ) {
        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " a positive integer, $port given.");
        }

        if ($timeout < 0.0) {
            throw new InvalidArgumentException('Timeout cannot be less'
                . " than 0, $timeout given.");
        }

        if ($retries < 0) {
            throw new InvalidArgumentException('The number of retries'
                . " cannot be less than 0, $retries given.");
        }

        if ($delay < 0.0) {
            throw new InvalidArgumentException('Retry delay cannot be'
                . " less than 0, $delay given.");
        }

        $serializer = $serializer ?? new JSON();

        $headers = array_replace($headers, self::SERIALIZER_HEADERS[get_class($serializer)]);

        $this->client = new Guzzle([
            'base_uri' => ($secure ? 'https' : 'http') . "://$host:$port",
            'headers' => $headers,
        ]);

        $this->timeout = $timeout;
        $this->retries = $retries;
        $this->delay = (int) round($delay * 1e6);
        $this->serializer = $serializer;
    }

    /**
     * Send a command to the server and return the results.
     *
     * @param \Rubix\Server\Commands\Command $command
     * @throws \RuntimeException
     * @return \Rubix\Server\Responses\Response
     */
    public function send(Command $command) : Response
    {
        $data = $this->serializer->serialize($command);

        $delay = $this->delay;

        $lastException = null;

        for ($tries = 1 + $this->retries; $tries > 0; --$tries) {
            try {
                $payload = $this->client->request('POST', '/', [
                    'body' => $data,
                    'timeout' => $this->timeout,
                ])->getBody();

                break 1;
            } catch (Exception $e) {
                usleep($delay);

                if ($delay < self::MAX_DELAY) {
                    $delay *= 2;
                }

                $lastException = $e;
            }
        }

        if (empty($payload)) {
            $message = $lastException ? $lastException->getMessage() : '';

            throw new RuntimeException('There was a problem communicating'
                . " with the server. $message");
        }

        $response = $this->serializer->unserialize($payload);

        if (!$response instanceof Response) {
            throw new RuntimeException('Message is not a valid response.');
        }

        return $response;
    }
}
