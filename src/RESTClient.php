<?php

namespace Rubix\Server;

use Rubix\ML\Datasets\Dataset;
use Rubix\Server\HTTP\Requests\PredictRequest;
use Rubix\Server\HTTP\Requests\ProbaRequest;
use Rubix\Server\HTTP\Requests\ScoreRequest;
use Rubix\Server\HTTP\Requests\GetDashboardRequest;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use Rubix\Server\Helpers\JSON;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Psr\Http\Message\ResponseInterface;
use Exception;

/**
 * REST Client
 *
 * The REST (Representational State Transfer) client communicates with a REST Server.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RESTClient implements Client, AsyncClient
{
    public const HEADERS = [
        'User-Agent' => 'Rubix ML REST Client/' . VERSION,
        'Accept' => 'application/json',
    ];

    protected const MAX_TCP_PORT = 65535;

    /**
     * The Guzzle HTTP client.
     *
     * @var Guzzle
     */
    protected $client;

    /**
     * @param string $host
     * @param int $port
     * @param bool $secure
     * @param mixed[] $headers
     * @param float $timeout
     * @param int $retries
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 80,
        bool $secure = false,
        array $headers = [],
        float $timeout = 0.0,
        int $retries = 3
    ) {
        if (empty($host)) {
            throw new InvalidArgumentException('Host address cannot be empty.');
        }

        if ($port < 0 or $port > self::MAX_TCP_PORT) {
            throw new InvalidArgumentException('Port number must be'
                . ' between 0 and ' . self::MAX_TCP_PORT . ", $port given.");
        }

        if ($timeout < 0.0) {
            throw new InvalidArgumentException('Timeout must be'
                . " greater than 0, $timeout given.");
        }

        if ($retries < 0) {
            throw new InvalidArgumentException('Number of retries'
                . " must be greater than 0, $retries given.");
        }

        $baseUri = ($secure ? 'https' : 'http') . "://$host:$port";

        $headers += self::HEADERS;

        $stack = HandlerStack::create();

        $stack->push(GuzzleRetryMiddleware::factory());

        $this->client = new Guzzle([
            'base_uri' => $baseUri,
            'headers' => $headers,
            'timeout' => $timeout,
            'max_retry_attempts' => $retries,
            'handler' => $stack,
        ]);
    }

    /**
     * Make a set of predictions on a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return (string|int|float)[]
     */
    public function predict(Dataset $dataset) : array
    {
        return $this->predictAsync($dataset)->wait();
    }

    /**
     * Make a set of predictions on a dataset and return a promise.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function predictAsync(Dataset $dataset) : PromiseInterface
    {
        $request = new PredictRequest($dataset);

        $unpackPayload = function (array $json) : Promise {
            if (empty($json['predictions'])) {
                throw new RuntimeException('Predictions missing'
                    . ' from response payload.');
            }

            $promise = new Promise(function () use (&$promise, $json) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($json['predictions']);
            });

            return $promise;
        };

        return $this->client->sendAsync($request)->then(
            [$this, 'parseResponseBody'],
            [$this, 'onError']
        )->then($unpackPayload);
    }

    /**
     * Return the joint probabilities of each sample in a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return array[]
     */
    public function proba(Dataset $dataset) : array
    {
        return $this->probaAsync($dataset)->wait();
    }

    /**
     * Compute the joint probabilities of the samples in a dataset and return a promise.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function probaAsync(Dataset $dataset) : PromiseInterface
    {
        $request = new ProbaRequest($dataset);

        $unpackPayload = function (array $json) : Promise {
            if (empty($json['probabilities'])) {
                throw new RuntimeException('Probabilities missing'
                    . ' from response payload.');
            }

            $promise = new Promise(function () use (&$promise, $json) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($json['probabilities']);
            });

            return $promise;
        };

        return $this->client->sendAsync($request)->then(
            [$this, 'parseResponseBody'],
            [$this, 'onError']
        )->then($unpackPayload);
    }

    /**
     * Return the anomaly scores of each sample in a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return float[]
     */
    public function score(Dataset $dataset) : array
    {
        return $this->scoreAsync($dataset)->wait();
    }

    /**
     * Compute the anomaly scores of the samples in a dataset and return a promise.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function scoreAsync(Dataset $dataset) : PromiseInterface
    {
        $request = new ScoreRequest($dataset);

        $unpackPayload = function (array $json) : Promise {
            if (empty($json['scores'])) {
                throw new RuntimeException('Anomaly scores missing'
                    . ' from response payload.');
            }

            $promise = new Promise(function () use (&$promise, $json) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($json['scores']);
            });

            return $promise;
        };

        return $this->client->sendAsync($request)->then(
            [$this, 'parseResponseBody'],
            [$this, 'onError']
        )->then($unpackPayload);
    }

    /**
     * Return the server dashboard information.
     *
     * @return mixed[]
     */
    public function getDashboard() : array
    {
        return $this->getDashboardAsync()->wait();
    }

    /**
     * Return a promise for the server dashboard information.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getDashboardAsync() : PromiseInterface
    {
        $request = new GetDashboardRequest();

        return $this->client->sendAsync($request)->then(
            [$this, 'parseResponseBody'],
            [$this, 'onError']
        );
    }

    /**
     * Parse the response body and return a promise that resolves to an associative array.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\Promise
     */
    public function parseResponseBody(ResponseInterface $response) : Promise
    {
        $promise = new Promise(function () use (&$promise, $response) {
            /** @var \GuzzleHttp\Promise\Promise $promise */
            $promise->resolve(JSON::decode($response->getBody()));
        });

        return $promise;
    }

    /**
     * Rethrow a client exception from the server namespace.
     *
     * @internal
     *
     * @param \Exception $exception
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function onError(Exception $exception) : void
    {
        throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
    }
}
