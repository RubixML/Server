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
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Client as Guzzle;

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
class RPCClient
{
    public const HTTP_HEADERS = [
        'User-Agent' => 'Rubix RPC Client',
    ];

    public const HTTP_METHOD = 'POST';

    public const HTTP_ENDPOINT = '/commands';

    protected const MAX_TCP_PORT = 65535;

    protected const IP_FLAGS = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;

    /**
     * The Guzzle client.
     *
     * @var Guzzle
     */
    protected $client;

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
     * @param \Rubix\Server\Serializers\Serializer|null $serializer
     * @param float $timeout
     * @param int $retries
     * @param float $delay
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
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
        if (filter_var($host, FILTER_VALIDATE_IP, self::IP_FLAGS) === false) {
            throw new InvalidArgumentException('Invalid IP address for host.');
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
            'timeout' => $timeout,
        ]);

        $this->serializer = $serializer;
        $this->retries = $retries;
        $this->delay = $delay;
    }

    /**
     * Make a set of predictions on a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Responses\PredictResponse
     */
    public function predict(Dataset $dataset) : PredictResponse
    {
        $response = $this->send(new Predict($dataset));

        if (!$response instanceof PredictResponse) {
            throw new RuntimeException('Invalid response returned.');
        }

        return $response;
    }

    /**
     * Make a single prediction on a sample.
     *
     * @param (string|int|float)[] $sample
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Responses\PredictSampleResponse
     */
    public function predictSample(array $sample) : PredictSampleResponse
    {
        $response = $this->send(new PredictSample($sample));

        if (!$response instanceof PredictSampleResponse) {
            throw new RuntimeException('Invalid response returned.');
        }

        return $response;
    }

    /**
     * Return the joint probabilities of each sample in a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Responses\ProbaResponse
     */
    public function proba(Dataset $dataset) : ProbaResponse
    {
        $response = $this->send(new Proba($dataset));

        if (!$response instanceof ProbaResponse) {
            throw new RuntimeException('Invalid response returned.');
        }

        return $response;
    }

    /**
     * Return the joint probabilities of a single sample.
     *
     * @param (string|int|float)[] $sample
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Responses\ProbaSampleResponse
     */
    public function probaSample(array $sample) : ProbaSampleResponse
    {
        $response = $this->send(new ProbaSample($sample));

        if (!$response instanceof ProbaSampleResponse) {
            throw new RuntimeException('Invalid response returned.');
        }

        return $response;
    }

    /**
     * Return the anomaly scores of the samples in a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Responses\ScoreResponse
     */
    public function score(Dataset $dataset) : ScoreResponse
    {
        $response = $this->send(new Score($dataset));

        if (!$response instanceof ScoreResponse) {
            throw new RuntimeException('Invalid response returned.');
        }

        return $response;
    }

    /**
     * Return the anomaly score of a single sample.
     *
     * @param (string|int|float)[] $sample
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Responses\ScoreSampleResponse
     */
    public function scoreSample(array $sample) : ScoreSampleResponse
    {
        $response = $this->send(new ScoreSample($sample));

        if (!$response instanceof ScoreSampleResponse) {
            throw new RuntimeException('Invalid response returned.');
        }

        return $response;
    }

    /**
     * Send a command to the server and return the results.
     *
     * @param \Rubix\Server\Commands\Command $command
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Responses\Response
     */
    public function send(Command $command) : Response
    {
        $data = $this->serializer->serialize($command);

        $maxTries = 1 + $this->retries;

        $delay = $this->delay;

        for ($tries = 0; $tries < $maxTries; ++$tries) {
            try {
                $response = $this->client->request(self::HTTP_METHOD, self::HTTP_ENDPOINT, [
                    'body' => $data,
                ]);

                break 1;
            } catch (BadResponseException $exception) {
                $code = $exception->getCode();

                if ($tries >= $maxTries) {
                    break 1;
                }

                if ($code === SERVICE_UNAVAILABLE or $code === TOO_MANY_REQUESTS) {
                    $response = $exception->getResponse();

                    if ($response->hasHeader('Retry-After')) {
                        $wait = (float) $response->getHeader('Retry-After')[0] ?? $delay;
                    } else {
                        $wait = $delay;
                    }

                    usleep((int) ($wait * 1e6));

                    $delay *= 2.0;

                    continue 1;
                }

                throw $exception;
            }
        }

        if (empty($response)) {
            throw new RuntimeException('No response from the server.');
        }

        $response = $this->serializer->unserialize($response->getBody());

        if (!$response instanceof Response) {
            throw new RuntimeException('Message is not a valid response.');
        }

        return $response;
    }
}
