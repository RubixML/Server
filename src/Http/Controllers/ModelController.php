<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Proba;
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
            $command = Predict::fromArray($json);
        } catch (Exception $exception) {
            $this->responseInvalid($exception);
        }

        return $this->bus->dispatch($command)->then(
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
            $command = Proba::fromArray($json);
        } catch (Exception $exception) {
            $this->respondInvalid($exception);
        }

        return $this->bus->dispatch($command)->then(
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
            $command = Score::fromArray($json);
        } catch (Exception $exception) {
            $this->respondInvalid($exception);
        }

        return $this->bus->dispatch($command)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
    }
}
