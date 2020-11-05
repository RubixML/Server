<?php

namespace Rubix\Server;

use Rubix\ML\Datasets\Dataset;
use Rubix\Server\Helpers\JSON;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Psr\Http\Message\ResponseInterface;

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
        $after = function (array $payload) : Promise {
            if (!isset($payload['predictions'])) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\PromiseInterface $promise */
                $promise->resolve($payload['predictions']);
            });

            return $promise;
        };

        return $this->client->postAsync(RESTServer::ENDPOINTS['predict'], [
            'json' => [
                'samples' => $dataset->samples(),
            ],
        ])->then(
            [$this, 'onFulfilled'],
            [$this, 'onRejected']
        )->then($after);
    }

    /**
     * Make a single prediction on a sample.
     *
     * @param (string|int|float)[] $sample
     * @return string|int|float
     */
    public function predictSample(array $sample)
    {
        return $this->predictSampleAsync($sample)->wait();
    }

    /**
     * Make a single prediction on a sample and return a promise.
     *
     * @param (string|int|float)[] $sample
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function predictSampleAsync(array $sample) : PromiseInterface
    {
        $after = function (array $payload) : Promise {
            if (!isset($payload['prediction'])) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\PromiseInterface $promise */
                $promise->resolve($payload['prediction']);
            });

            return $promise;
        };

        return $this->client->postAsync(RESTServer::ENDPOINTS['predict_sample'], [
            'json' => [
                'sample' => $sample,
            ],
        ])->then(
            [$this, 'onFulfilled'],
            [$this, 'onRejected']
        )->then($after);
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
        $after = function (array $payload) : Promise {
            if (!isset($payload['probabilities'])) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\PromiseInterface $promise */
                $promise->resolve($payload['probabilities']);
            });

            return $promise;
        };

        return $this->client->postAsync(RESTServer::ENDPOINTS['proba'], [
            'json' => [
                'samples' => $dataset->samples(),
            ],
        ])->then(
            [$this, 'onFulfilled'],
            [$this, 'onRejected']
        )->then($after);
    }

    /**
     * Return the joint probabilities of a single sample.
     *
     * @param (string|int|float)[] $sample
     * @return float[]
     */
    public function probaSample(array $sample) : array
    {
        return $this->probaSampleAsync($sample)->wait();
    }

    /**
     * Compute the joint probabilities of a single sample and return a promise.
     *
     * @param (string|int|float)[] $sample
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function probaSampleAsync(array $sample) : PromiseInterface
    {
        $after = function (array $payload) : Promise {
            if (!isset($payload['probabilities'])) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\PromiseInterface $promise */
                $promise->resolve($payload['probabilities']);
            });

            return $promise;
        };

        return $this->client->postAsync(RESTServer::ENDPOINTS['proba_sample'], [
            'json' => [
                'sample' => $sample,
            ],
        ])->then(
            [$this, 'onFulfilled'],
            [$this, 'onRejected']
        )->then($after);
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
        $after = function (array $payload) : Promise {
            if (!isset($payload['scores'])) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\PromiseInterface $promise */
                $promise->resolve($payload['scores']);
            });

            return $promise;
        };

        return $this->client->postAsync(RESTServer::ENDPOINTS['score'], [
            'json' => [
                'samples' => $dataset->samples(),
            ],
        ])->then(
            [$this, 'onFulfilled'],
            [$this, 'onRejected']
        )->then($after);
    }

    /**
     * Return the anomaly score of a single sample.
     *
     * @param (string|int|float)[] $sample
     * @return float
     */
    public function scoreSample(array $sample) : float
    {
        return $this->scoreSampleAsync($sample)->wait();
    }

    /**
     * Compute the anomaly scores of a single sample and return a promise.
     *
     * @param (string|int|float)[] $sample
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function scoreSampleAsync(array $sample) : PromiseInterface
    {
        $after = function (array $payload) : Promise {
            if (!isset($payload['score'])) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\PromiseInterface $promise */
                $promise->resolve($payload['score']);
            });

            return $promise;
        };

        return $this->client->postAsync(RESTServer::ENDPOINTS['score_sample'], [
            'json' => [
                'sample' => $sample,
            ],
        ])->then(
            [$this, 'onFulfilled'],
            [$this, 'onRejected']
        )->then($after);
    }

    /**
     * The callback to execute when the request promise is fulfilled.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\Promise
     */
    public function onFulfilled(ResponseInterface $response) : Promise
    {
        $promise = new Promise(function () use (&$promise, $response) {
            $payload = JSON::decode($response->getBody());

            /** @var \GuzzleHttp\Promise\PromiseInterface $promise */
            $promise->resolve($payload);
        });

        return $promise;
    }

    /**
     * The callback to execute when the request promise is rejected.
     *
     * @internal
     *
     * @param \GuzzleHttp\Exception\GuzzleException $exception
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\Promise
     */
    public function onRejected(GuzzleException $exception) : Promise
    {
        throw $exception;
    }
}
