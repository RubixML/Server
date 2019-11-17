<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use Rubix\Server\Responses\Response;
use Rubix\Server\Serializers\Json;
use Rubix\Server\Serializers\Binary;
use Rubix\Server\Serializers\Native;
use Rubix\Server\Serializers\Serializer;
use GuzzleHttp\Client as Guzzle;
use InvalidArgumentException;
use RuntimeException;
use Exception;

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
    protected const SERIALIZER_HEADERS = [
        Json::class => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        Native::class => [
            'Content-Type' => 'application/octet-stream',
            'Accept' => 'application/octet-stream',
        ],
        Binary::class => [
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
     * The number of microseconds to wait before retrying a request.
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
     * @param array $headers
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
        float $timeout = 0.,
        int $retries = 2,
        float $delay = 0.3
    ) {
        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " a positive integer, $port given.");
        }

        if ($timeout < 0.) {
            throw new InvalidArgumentException('Timeout cannot be less'
                . " than 0, $timeout given.");
        }

        if ($retries < 0) {
            throw new InvalidArgumentException('The number of retries'
                . " cannot be less than 0, $retries given.");
        }

        if ($delay < 0.) {
            throw new InvalidArgumentException('Retry delay cannot be'
                . " less than 0, $delay given.");
        }

        $serializer = $serializer ?? new Json();

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
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Rubix\Server\Responses\Response
     */
    public function send(Command $command) : Response
    {
        $data = $this->serializer->serialize($command);

        $tries = 1 + $this->retries;

        while ($tries) {
            try {
                $payload = $this->client->request('POST', '/', [
                    'body' => $data,
                    'timeout' => $this->timeout,
                ])->getBody();

                break 1;
            } catch (Exception $e) {
                if ((int) round($e->getCode(), -2) === 400) {
                    throw $e;
                }

                --$tries;
                
                if ($tries) {
                    usleep($this->delay);
                }
            }
        }

        if (empty($payload)) {
            throw new RuntimeException('There was a problem'
                . ' communicating with the server.');
        }

        $response = $this->serializer->unserialize($payload);

        if (!$response instanceof Response) {
            throw new RuntimeException('Message is not a valid response.');
        }

        return $response;
    }
}
