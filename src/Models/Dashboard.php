<?php

namespace Rubix\Server\Models;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Dashboard
{
    protected const MEGA_BYTE = 1000000;

    /**
     * The number of requests received so far.
     *
     * @var int
     */
    protected $numRequests;

    /**
     * The number of successful requests handled by the server.
     *
     * @var int
     */
    protected $successfulResponses;

    /**
     * The number of failed requests handled by the server.
     *
     * @var int
     */
    protected $failedResponses;

    /**
     * The timestamp from when the server went up.
     *
     * @var int
     */
    protected $start;

    public function __construct()
    {
        $this->numRequests = 0;
        $this->successfulResponses = 0;
        $this->failedResponses = 0;
        $this->start = time();
    }

    /**
     * Increment the request counter.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $response
     * @return self
     */
    public function incrementRequestCount(ServerRequestInterface $response) : self
    {
        ++$this->numRequests;

        return $this;
    }

    /**
     * Increment the response counter for a given response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return self
     */
    public function incrementResponseCount(ResponseInterface $response) : self
    {
        switch ($response->getStatusCode()) {
            case 200:
                ++$this->successfulResponses;

                break 1;

            default:
                ++$this->failedResponses;
        }

        return $this;
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
     * Return the current number of requests handled per minute.
     *
     * @return float
     */
    public function requestsPerMinute() : float
    {
        return $this->handledRequests() / ($this->uptime() / 60);
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

    /**
     * Return the current memory usage of the server in mega bytes (MB).
     *
     * @return float
     */
    public function memoryUsage() : float
    {
        return memory_get_usage() / self::MEGA_BYTE;
    }

    /**
     * Return the peak memory usage of the server in mega bytes (MB).
     *
     * @return float
     */
    public function memoryPeak() : float
    {
        return memory_get_peak_usage() / self::MEGA_BYTE;
    }

    /**
     * Return the number of seconds the server has been up.
     *
     * @return int
     */
    public function uptime() : int
    {
        return time() - $this->start;
    }
}
