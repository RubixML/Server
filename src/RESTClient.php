<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;
use GuzzleHttp\Client as Guzzle;
use InvalidArgumentException;

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
    }

    /**
     * Send a command to the server and return the results.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @return array
     */
    public function send(Command $command) : array
    {
        list($method, $uri) = self::ROUTES[get_class($command)];

        $response = $this->client->request($method, $uri, [
            'json' => $command->payload(),
        ]);

        return json_decode($response->getBody(), true);
    }
}