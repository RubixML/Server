<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use Rubix\Server\Responses\Response;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use RuntimeException;

use const Rubix\Server\Http\SERVICE_UNAVAILABLE;
use const Rubix\Server\Http\TOO_MANY_REQUESTS;

/**
 * RPC Client
 *
 * The RPC Client is made to communicate with a RPC Server over HTTP or Secure HTTP (HTTPS). In
 * the event of a network failure, it uses a backoff and retry mechanism as a failover strategy.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RPCClient implements Client
{
    public const HTTP_HEADERS = [
        'User-Agent' => 'Rubix RPC Client',
    ];

    public const HTTP_METHOD = 'POST';

    public const HTTP_ENDPOINT = '/commands';

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
     * @var float
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
        int $retries = 3,
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

        $baseUri = ($secure ? 'https' : 'http') . "://$host:$port";

        $headers += self::HTTP_HEADERS + $serializer->headers();

        $this->client = new Guzzle([
            'base_uri' => $baseUri,
            'headers' => $headers,
        ]);

        $this->serializer = $serializer;
        $this->timeout = $timeout;
        $this->retries = $retries;
        $this->delay = $delay;
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

        $maxTries = 1 + $this->retries;

        $delay = $this->delay;

        $lastException = null;

        for ($tries = 1; $tries <= $maxTries; ++$tries) {
            try {
                $payload = $this->client->request(self::HTTP_METHOD, self::HTTP_ENDPOINT, [
                    'body' => $data,
                    'timeout' => $this->timeout,
                ])->getBody();

                break 1;
            } catch (ClientException $e) {
                $code = $e->getCode();

                $lastException = $e;

                if ($code === SERVICE_UNAVAILABLE or $code === TOO_MANY_REQUESTS) {
                    $response = $e->getResponse();

                    if ($response->hasHeader('Retry-After')) {
                        $wait = (float) $response->getHeader('Retry-After')[0] ?? $delay;
                    } else {
                        $wait = $delay;
                    }

                    $delay *= 2.0;

                    usleep(intval($wait * 1e6));

                    continue 1;
                }

                break 1;
            }
        }

        if (empty($payload)) {
            $message = $lastException ? $lastException->getMessage() : '';

            throw new RuntimeException($message);
        }

        $response = $this->serializer->unserialize($payload);

        if (!$response instanceof Response) {
            throw new RuntimeException('Message is not a valid response.');
        }

        return $response;
    }
}
