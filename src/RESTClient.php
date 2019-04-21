<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\Rank;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Responses\Response;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Binary;
use Rubix\Server\Serializers\Native;
use Rubix\Server\Serializers\Serializer;
use GuzzleHttp\Client as Guzzle;
use InvalidArgumentException;
use RuntimeException;
use Exception;

/**
 * REST Client
 *
 * The REST Client is made to communicate with a REST Server over HTTP or
 * Secure HTTP (HTTPS).
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RESTClient implements Client
{
    protected const URLS = [
        QueryModel::class => RESTServer::MODEL_PREFIX,
        ServerStatus::class => RESTServer::SERVER_PREFIX . RESTServer::SERVER_STATUS_ENDPOINT,
        Predict::class => RESTServer::MODEL_PREFIX . RESTServer::PREDICT_ENDPOINT,
        Proba::class => RESTServer::MODEL_PREFIX . RESTServer::PROBA_ENDPOINT,
        Rank::class => RESTServer::MODEL_PREFIX . RESTServer::RANK_ENDPOINT,
    ];

    protected const ROUTES = [
        QueryModel::class => ['GET', self::URLS[QueryModel::class]],
        Predict::class => ['POST', self::URLS[Predict::class]],
        Proba::class => ['POST', self::URLS[Proba::class]],
        Rank::class => ['POST', self::URLS[Rank::class]],
        ServerStatus::class => ['GET', self::URLS[ServerStatus::class]],
    ];

    protected const SERIALIZER_HEADERS = [
        JSON::class => [
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
        float $timeout = 0.,
        int $retries = 2,
        float $delay = 0.3,
        ?Serializer $serializer = null
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

        $serializer = $serializer ?? new JSON();

        $headers = array_replace(self::SERIALIZER_HEADERS[get_class($serializer)], $headers);

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
        $classname = get_class($command);

        if (!isset(self::ROUTES[$classname])) {
            throw new InvalidArgumentException('Command is missing'
                . ' from routing table.');
        }

        [$method, $uri] = self::ROUTES[$classname];

        $tries = 1 + $this->retries;

        $body = $this->serializer->serialize($command);

        do {
            try {
                $data = $this->client->request($method, $uri, [
                    'body' => $body,
                    'timeout' => $this->timeout,
                ])->getBody();

                break 1;
            } catch (Exception $e) {
                if ((int) round($e->getCode(), -2) === 400) {
                    throw $e;
                }

                $tries--;
                
                if ($tries > 0) {
                    usleep($this->delay);
                }
            }
        } while ($tries > 0);

        if (!isset($data)) {
            throw new RuntimeException('There was a problem'
            . ' communicating with the server.');
        }

        $response = $this->serializer->unserialize($data);

        if (!$response instanceof Response) {
            throw new RuntimeException('Response could not'
                . ' be reconstituted.');
        }

        return $response;
    }
}
