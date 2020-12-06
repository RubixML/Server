<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Services\EventBus;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\UnprocessableEntity;
use Rubix\Server\HTTP\Responses\InternalServerError;
use Rubix\Server\Events\ModelQueryFailed;
use Rubix\Server\Exceptions\ValidationException;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use Rubix\Server\Helpers\JSON;
use React\Promise\Promise;
use Exception;

use Psr\Http\Message\ServerRequestInterface;

class ModelController extends JSONController
{
    /**
     * The model that is being served.
     *
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * The event bus.
     *
     * @var \Rubix\Server\Services\EventBus
     */
    protected $eventBus;

    /**
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\Services\EventBus $eventBus
     */
    public function __construct(Estimator $estimator, EventBus $eventBus)
    {
        if ($estimator instanceof Learner) {
            if (!$estimator->trained()) {
                throw new InvalidArgumentException('Learner must be trained.');
            }
        }

        $this->estimator = $estimator;
        $this->eventBus = $eventBus;
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        $routes = [
            '/model/predictions' => [
                'POST' => [
                    [$this, 'decompressRequestBody'],
                    [$this, 'parseRequestBody'],
                    [$this, 'predict'],
                ],
            ],
        ];

        if ($this->estimator instanceof Probabilistic) {
            $routes['/model/probabilities'] = [
                'POST' => [
                    [$this, 'decompressRequestBody'],
                    [$this, 'parseRequestBody'],
                    [$this, 'proba'],
                ],
            ];
        }

        if ($this->estimator instanceof Ranking) {
            $routes['/model/anomaly_scores'] = [
                'POST' => [
                    [$this, 'decompressRequestBody'],
                    [$this, 'parseRequestBody'],
                    [$this, 'score'],
                ],
            ];
        }

        return $routes;
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function predict(ServerRequestInterface $request)
    {
        /** @var mixed[] $input */
        $input = $request->getParsedBody();

        try {
            if (empty($input['samples'])) {
                throw new ValidationException('Samples property must not be empty.');
            }

            $dataset = new Unlabeled($input['samples']);
        } catch (Exception $exception) {
            return new UnprocessableEntity(self::DEFAULT_HEADERS, JSON::encode([
                'message' => $exception->getMessage(),
            ]));
        }

        $promise = new Promise(function ($resolve) use ($dataset) {
            $predictions = $this->estimator->predict($dataset);

            $response = new Success(self::DEFAULT_HEADERS, JSON::encode([
                'data' => [
                    'predictions' => $predictions,
                ],
            ]));

            $resolve($response);
        });

        return $promise->otherwise([$this, 'onError']);
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function proba(ServerRequestInterface $request)
    {
        /** @var mixed[] $input */
        $input = $request->getParsedBody();

        try {
            if (empty($input['samples'])) {
                throw new ValidationException('Samples property must not be empty.');
            }

            $dataset = new Unlabeled($input['samples']);
        } catch (Exception $exception) {
            return new UnprocessableEntity(self::DEFAULT_HEADERS, JSON::encode([
                'message' => $exception->getMessage(),
            ]));
        }

        $promise = new Promise(function ($resolve) use ($dataset) {
            if (!$this->estimator instanceof Probabilistic) {
                throw new RuntimeException('Estimator must implement'
                    . ' the Probabilistic interface.');
            }

            $probabilities = $this->estimator->proba($dataset);

            $response = new Success(self::DEFAULT_HEADERS, JSON::encode([
                'data' => [
                    'probabilities' => $probabilities,
                ],
            ]));

            $resolve($response);
        });

        return $promise->otherwise([$this, 'onError']);
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function score(ServerRequestInterface $request)
    {
        /** @var mixed[] $input */
        $input = $request->getParsedBody();

        try {
            if (empty($input['samples'])) {
                throw new ValidationException('Samples property must not be empty.');
            }

            $dataset = new Unlabeled($input['samples']);
        } catch (Exception $exception) {
            return new UnprocessableEntity(self::DEFAULT_HEADERS, JSON::encode([
                'message' => $exception->getMessage(),
            ]));
        }

        $promise = new Promise(function ($resolve) use ($dataset) {
            if (!$this->estimator instanceof Ranking) {
                throw new RuntimeException('Estimator must implement'
                    . ' the Ranking interface.');
            }

            $scores = $this->estimator->score($dataset);

            $response = new Success(self::DEFAULT_HEADERS, JSON::encode([
                'data' => [
                    'scores' => $scores,
                ],
            ]));

            $resolve($response);
        });

        return $promise->otherwise([$this, 'onError']);
    }

    /**
     * Respond with an internal server error.
     *
     * @param \Exception $exception
     * @return \Rubix\Server\HTTP\Responses\InternalServerError
     */
    public function onError(Exception $exception) : InternalServerError
    {
        $this->eventBus->dispatch(new ModelQueryFailed($exception));

        return new InternalServerError();
    }
}
