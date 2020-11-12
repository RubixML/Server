<?php

namespace Rubix\Server;

use Rubix\ML\Datasets\Dataset;
use Rubix\Server\Http\Requests\CommandRequest;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\Score;
use Rubix\Server\Payloads\Payload;
use Rubix\Server\Payloads\PredictPayload;
use Rubix\Server\Payloads\ProbaPayload;
use Rubix\Server\Payloads\ScorePayload;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Psr\Http\Message\ResponseInterface;
use Exception;

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
        $after = function (Payload $payload) : Promise {
            if (!$payload instanceof PredictPayload) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($payload->predictions());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new Predict($dataset))->then($after);
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
        $after = function (Payload $payload) : Promise {
            if (!$payload instanceof ProbaPayload) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($payload->probabilities());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new Proba($dataset))->then($after);
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
        $after = function (Payload $payload) : Promise {
            if (!$payload instanceof ScorePayload) {
                throw new RuntimeException('Invalid response returned.');
            }

            $promise = new Promise(function () use (&$promise, $payload) {
                /** @var \GuzzleHttp\Promise\Promise $promise */
                $promise->resolve($payload->scores());
            });

            return $promise;
        };

        return $this->sendCommandAsync(new Score($dataset))->then($after);
    }

    /**
     * Parse the response body and return a promise that resolves to a payload object.
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
            $payload = $this->serializer->unserialize($response->getBody());

            if (!$payload instanceof Payload) {
                throw new RuntimeException('Message is not a valid response.');
            }

            /** @var \GuzzleHttp\Promise\Promise $promise */
            $promise->resolve($payload);
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

    /**
     * Send a command to the server and return the results.
     *
     * @param \Rubix\Server\Commands\Command $command
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function sendCommandAsync(Command $command) : PromiseInterface
    {
        $request = new CommandRequest($command, $this->serializer);

        return $this->client->sendAsync($request)->then(
            [$this, 'parseResponseBody'],
            [$this, 'handleException']
        );
    }
}
