<?php

namespace Rubix\Server\Models;

use Psr\Http\Message\ResponseInterface;

class Dashboard
{
    /**
     * The number of successful requests handled by the server.
     * 
     * @var int
     */
    protected $numSuccessful;

    /**
     * The number of failed requests handled by the server.
     * 
     * @var int
     */
    protected $numFailed;

    /**
     * The timestamp from when the server went up.
     * 
     * @var int
     */
    protected $start;

    public function __construct()
    {
        $this->numSuccessful = 0;
        $this->start = time();
    }

    /**
     * Increment the response counter for a given response.
     * 
     * @return self
     */
    public function incrementResponseCounter(ResponseInterface $response) : self
    {
        switch ($response->getStatusCode()) {
            case 200:
                ++$this->numSuccessful;

                break 1;

            default:
                ++$this->numFailed;
        }

        return $this;
    }

    /**
     * Return the total number of requests handled by the server.
     * 
     * @return int
     */
    public function numRequests() : int
    {
        return $this->numSuccessful + $this->numFailed;
    }

    /**
     * Return the number of successful requests handled by the server.
     * 
     * @return int
     */
    public function numSuccessful() : int
    {
        return $this->numSuccessful;
    }

    /**
     * Return the number of failed requests handled by the server.
     * 
     * @return int
     */
    public function numFailed() : int
    {
        return $this->numFailed;
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

    /**
     * Return the current requests per minute.
     * 
     * @return float
     */
    public function requestsPerMinute() : float
    {
        return $this->numRequests() / ($this->uptime() / 60);
    }
}