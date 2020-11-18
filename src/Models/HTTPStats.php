<?php

namespace Rubix\Server\Models;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class HTTPStats
{
    /**
     * The number of requests received so far.
     *
     * @var int
     */
    protected $numRequests = 0;

    /**
     * The number of successful requests handled by the server.
     *
     * @var int
     */
    protected $successfulResponses = 0;

    /**
     * The number of failed requests handled by the server.
     *
     * @var int
     */
    protected $failedResponses = 0;

    /**
     * Increment the request counter.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function incrementRequestCount(ServerRequestInterface $request) : void
    {
        ++$this->numRequests;
    }

    /**
     * Increment the response counter for a given response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function incrementResponseCount(ResponseInterface $response) : void
    {
        switch ($response->getStatusCode()) {
            case 200:
                ++$this->successfulResponses;

                break 1;

            default:
                ++$this->failedResponses;
        }
    }

    /**
     * Return the number of requests received so far.
     *
     * @return int
     */
    public function numRequests() : int
    {
        return $this->numRequests;
    }

    /**
     * Return the total number of requests handled by the server.
     *
     * @return int
     */
    public function handledRequests() : int
    {
        return $this->successfulResponses + $this->failedResponses;
    }

    /**
     * Return the number of successful requests handled by the server.
     *
     * @return int
     */
    public function successfulResponses() : int
    {
        return $this->successfulResponses;
    }

    /**
     * Return the number of failed requests handled by the server.
     *
     * @return int
     */
    public function failedResponses() : int
    {
        return $this->failedResponses;
    }
}
