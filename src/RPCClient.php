<?php

namespace Rubix\Server;

use Rubix\ML\Datasets\Dataset;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\ProbaSample;
use Rubix\Server\Commands\Score;
use Rubix\Server\Commands\ScoreSample;
use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\PredictResponse;
use Rubix\Server\Responses\PredictSampleResponse;
use Rubix\Server\Responses\ProbaResponse;
use Rubix\Server\Responses\ProbaSampleResponse;
use Rubix\Server\Responses\ScoreResponse;
use Rubix\Server\Responses\ScoreSampleResponse;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
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
 * RPC Client
 *
 * The RPC Client provides methods for querying an RPC Server over HTTP or Secure HTTP (HTTPS).
 * In addition, the RPC client uses a back-pressure mechanism to ensure that clients do not
 * overwhelm the server under heavy load.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RPCClient implements Client, AsyncClient
{
    public const HTTP_HEADERS = [
        'User-Agent' => 'Rubix RPC Client',
    ];

    public const ROUTES = [
        'commands' => ['POST', '/commands'],
    ];

    protected const MAX_TCP_PORT = 65535;

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
     * @param string $host
     * @param int $port
     * @param bool $secure
     * @param mixed[] $headers
     * @param \Rubix\Server\Serializers\Serializer|null $serializer
     * @param float $timeout
     * @param int $retries
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 8888,
        bool $secure = false,
        array $headers = [],
        ?Serializer $serializer = null,
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

        $serializer = $serializer ?? new JSON();

        $baseUri = ($secure ? 'https' : 'http') . "://$host:$port";

        $headers += self::HTTP_HEADERS + $serializer->headers();

        $stack = HandlerStack::create();

        $stack->push(GuzzleRetryMiddleware::factory());

        $this->client = new Guzzle([
            'base_uri' => $baseUri,
            'headers' => $headers,
            'timeout' => $timeout,
            'max_retry_attempts' => $retries,
            'handler' => $stack,
        ]);

        $this->serializer = $serializer;
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
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function predictAsync(Dataset $dataset) : PromiseInterface
    {
        $after = function (Response $response) : Promise {
            if (!$response instanceof PredictResponse) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $response) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($response->predictions());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new Predict($dataset))->then($after);
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
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function predictSampleAsync(array $sample) : PromiseInterface
    {
        $after = function (Response $response) : Promise {
            if (!$response instanceof PredictSampleResponse) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $response) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($response->prediction());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new PredictSample($sample))->then($after);
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
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function probaAsync(Dataset $dataset) : PromiseInterface
    {
        $after = function (Response $response) : Promise {
            if (!$response instanceof ProbaResponse) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $response) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($response->probabilities());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new Proba($dataset))->then($after);
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
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function probaSampleAsync(array $sample) : PromiseInterface
    {
        $after = function (Response $response) : Promise {
            if (!$response instanceof ProbaSampleResponse) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $response) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($response->probabilities());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new ProbaSample($sample))->then($after);
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
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function scoreAsync(Dataset $dataset) : PromiseInterface
    {
        $after = function (Response $response) : Promise {
            if (!$response instanceof ScoreResponse) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $response) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($response->scores());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new Score($dataset))->then($after);
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
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function scoreSampleAsync(array $sample) : PromiseInterface
    {
        $after = function (Response $response) : Promise {
            if (!$response instanceof ScoreSampleResponse) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $response) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($response->score());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new ScoreSample($sample))->then($after);
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
            $response = $this->serializer->unserialize($response->getBody());

            if (!$response instanceof Response) {
                throw new RuntimeException('Message is not a valid response.');
            }

            /** @var \GuzzleHttp\Promise\Promise $promise */
            $promise->resolve($response);
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

    /**
     * Send a command to the server and return the results.
     *
     * @param \Rubix\Server\Commands\Command $command
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function sendCommandAsync(Command $command) : PromiseInterface
    {
        $data = $this->serializer->serialize($command);

        [$method, $uri] = self::ROUTES['commands'];

        return $this->client->requestAsync($method, $uri, [
            'body' => $data,
        ])->then(
            [$this, 'onFulfilled'],
            [$this, 'onRejected']
        );
    }
}
