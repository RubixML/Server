<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Responses\Response;
use Rubix\Server\Serializers\Json;
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
    const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    const ROUTES = [
        QueryModel::class => ['GET', RESTServer::MODEL_PREFIX],
        Predict::class => ['POST', RESTServer::MODEL_PREFIX . RESTServer::PREDICT_ENDPOINT],
        Proba::class => ['POST', RESTServer::MODEL_PREFIX . RESTServer::PROBA_ENDPOINT],
        ServerStatus::class => ['GET', RESTServer::SERVER_PREFIX . RESTServer::SERVER_STATUS_ENDPOINT],
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
     * The serializer used to serialize/unserialize messages before
     * and after transit.
     * 
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * @param  string  $host
     * @param  int  $port
     * @param  bool  $secure
     * @param  array  $headers
     * @param  float  $timeout
     * @param  int  $retries
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(string $host = '127.0.0.1', int $port = 8888, bool $secure = false,
                                array $headers = [], float $timeout = 0., int $retries = 2)
    {
        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " a positive integer, $port given.");
        }

        $headers = array_replace(self::DEFAULT_HEADERS, $headers);

        if ($timeout < 0.) {
            throw new InvalidArgumentException('Timeout cannot be less'
                . "than 0, $timeout given.");
        }

        if ($retries < 0) {
            throw new InvalidArgumentException('The number of retries'
                . " cannot be less than 0, $retries given.");
        }

        $this->client = new Guzzle([
            'base_uri' => ($secure ? 'https' : 'http') . "://$host:$port",
            'headers' => $headers,
        ]);

        $this->timeout = $timeout;
        $this->retries = $retries;

        $this->serializer = new Json();
    }

    /**
     * Send a command to the server and return the results.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @throws \RuntimeException
     * @return \Rubix\Server\Responses\Response
     */
    public function send(Command $command) : Response
    {
        list($method, $uri) = self::ROUTES[get_class($command)];

        $retries = $this->retries;

        do {
            try {
                $data = $this->client->request($method, $uri, [
                    'json' => $command->asArray(),
                    'timeout' => $this->timeout,
                ])->getBody();

                break 1;
            } catch (Exception $e) {
                if (round($e->getCode(), -2) == 400) {
                    throw $e;
                }

                $retries--;
            }
        } while ($retries >= 0);

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