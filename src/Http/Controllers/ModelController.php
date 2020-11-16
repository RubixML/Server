<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Queries\Predict;
use Rubix\Server\Queries\Proba;
use Rubix\Server\Queries\Score;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class ModelController extends RESTController
{
    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/model/predictions' => [
                'POST' => [$this, 'predict'],
            ],
            '/model/probabilities' => [
                'POST' => [$this, 'proba'],
            ],
            '/model/anomaly_scores' => [
                'POST' => [$this, 'score'],
            ],
        ];
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function predict(ServerRequestInterface $request)
    {
        $json = (array) $request->getParsedBody();

        try {
            $query = Predict::fromArray($json);
        } catch (Exception $exception) {
            $this->responseInvalid($exception);
        }

        return $this->queryBus->dispatch($query)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function proba(ServerRequestInterface $request)
    {
        $json = (array) $request->getParsedBody();

        try {
            $query = Proba::fromArray($json);
        } catch (Exception $exception) {
            $this->respondInvalid($exception);
        }

        return $this->queryBus->dispatch($query)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function score(ServerRequestInterface $request)
    {
        $json = (array) $request->getParsedBody();

        try {
            $query = Score::fromArray($json);
        } catch (Exception $exception) {
            $this->respondInvalid($exception);
        }

        return $this->queryBus->dispatch($query)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
    }

    /**
     * Send the payload in a successful response.
     *
     * @internal
     *
     * @param \Rubix\Server\Payloads\Payload $payload
     * @return \Rubix\Server\Http\Responses\Success
     */
    public function respondSuccess(Payload $payload) : Success
    {
        $data = JSON::encode($payload->asArray());

        return new Success(self::HEADERS, $data);
    }
}
