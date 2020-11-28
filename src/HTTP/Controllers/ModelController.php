<?php

namespace Rubix\Server\HTTP\Controllers;

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
                'POST' => [
                    [$this, 'decompressRequestBody'],
                    [$this, 'parseRequestBody'],
                    [$this, 'predict'],
                ],
            ],
            '/model/probabilities' => [
                'POST' => [
                    [$this, 'decompressRequestBody'],
                    [$this, 'parseRequestBody'],
                    [$this, 'proba'],
                ],
            ],
            '/model/anomaly_scores' => [
                'POST' => [
                    [$this, 'decompressRequestBody'],
                    [$this, 'parseRequestBody'],
                    [$this, 'score'],
                ],
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
        /** @var mixed[] $body */
        $body = $request->getParsedBody();

        try {
            $query = Predict::fromArray($body);
        } catch (Exception $exception) {
            return $this->respondInvalid($exception);
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
        /** @var mixed[] $body */
        $body = $request->getParsedBody();

        try {
            $query = Proba::fromArray($body);
        } catch (Exception $exception) {
            return $this->respondInvalid($exception);
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
        /** @var mixed[] $body */
        $body = $request->getParsedBody();

        try {
            $query = Score::fromArray($body);
        } catch (Exception $exception) {
            return $this->respondInvalid($exception);
        }

        return $this->queryBus->dispatch($query)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
    }
}
