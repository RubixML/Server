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
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(string $host = '127.0.0.1', int $port = 8888, bool $secure = false,
                                array $headers = [])
    {
        if ($port < 0) {
            throw new InvalidArgumentException('Port number must be'
                . " a positive integer, $port given.");
        }

        $this->client = new Guzzle([
            'base_uri' => ($secure ? 'https' : 'http') . "://$host:$port",
            'headers' => $headers,
        ]);

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

        $data = $this->client->request($method, $uri, [
            'json' => $command,
        ])->getBody();

        $response = $this->serializer->unserialize($data);

        if (!$response instanceof Response) {
            throw new RuntimeException('Response could not'
                . ' be reconstituted.');
        }

        return $response;
    }
}