<?php

namespace Rubix\Server;

use Rubix\ML\Datasets\Dataset;
use Rubix\Server\Http\Requests\PredictRequest;
use Rubix\Server\Http\Requests\ProbaRequest;
use Rubix\Server\Http\Requests\ScoreRequest;
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
    public const HTTP_HEADERS = [
        'User-Agent' => 'Rubix REST Client',
    ];

    protected const MAX_TCP_PORT = 65535;

    /**
     * The Guzzle client.
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
        int $port = 8888,
        bool $secure = false,
        array $headers = [],
        float $timeout = 0.0,
        int $retries = 3
    ) {
        if (empty($host)) {
            throw new InvalidArgumentException('Host cannot be empty.');
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

        $headers += self::HTTP_HEADERS;

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
        $validateResponse = function (array $json) : Promise {
            if (empty($json['predictions'])) {
                throw new RuntimeException('Predictions missing'
                    . ' in response payload.');
            }

            $promise = new Promise(function () use (&$promise, $json) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($json['predictions']);
            });

            return $promise;
        };

        $request = new PredictRequest($dataset);

        return $this->client->sendAsync($request)->then(
            [$this, 'parseResponseBody'],
            [$this, 'handleException']
        )->then($validateResponse);
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
        $validateResponse = function (array $json) : Promise {
            if (empty($json['probabilities'])) {
                throw new RuntimeException('Probabilities missing'
                    . ' in response payload.');
            }

            $promise = new Promise(function () use (&$promise, $json) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($json['probabilities']);
            });

            return $promise;
        };

        $request = new ProbaRequest($dataset);

        return $this->client->sendAsync($request)->then(
            [$this, 'parseResponseBody'],
            [$this, 'handleException']
        )->then($validateResponse);
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
        $validateResponse = function (array $json) : Promise {
            if (empty($json['scores'])) {
                throw new RuntimeException('Anomaly scores missing'
                    . ' in response payload.');
            }

            $promise = new Promise(function () use (&$promise, $json) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($json['scores']);
            });

            return $promise;
        };

        $request = new ScoreRequest($dataset);

        return $this->client->sendAsync($request)->then(
            [$this, 'parseResponseBody'],
            [$this, 'handleException']
        )->then($validateResponse);
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
            $json = JSON::decode($response->getBody());

            /** @var \GuzzleHttp\Promise\Promise $promise */
            $promise->resolve($json);
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
    public function handleException(Exception $exception) : void
    {
        throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
    }
}
