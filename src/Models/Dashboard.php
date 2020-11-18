<?php

namespace Rubix\Server\Models;

class Dashboard
{
    protected const ONE_MINUTE = 60;

    /**
     * The request/response statistics.
     *
     * @var \Rubix\Server\Models\HTTPStats
     */
    protected $httpStats;

    /**
     * The memory model.
     *
     * @var \Rubix\Server\Models\Memory
     */
    protected $memory;

    /**
     * The timestamp from when the server went up.
     *
     * @var int
     */
    protected $start;

    public function __construct()
    {
        $this->httpStats = new HTTPStats();
        $this->memory = new Memory();
        $this->start = time();
    }

    /**
     * Return the HTTP stats model.
     *
     * @return \Rubix\Server\Models\HTTPStats
     */
    public function httpStats() : HTTPStats
    {
        return $this->httpStats;
    }

    /**
     * Return the current number of requests handled per minute.
     *
     * @return float
     */
    public function requestsPerMinute() : float
    {
        return $this->httpStats->handledRequests() / ($this->uptime() / self::ONE_MINUTE);
    }

    /**
     * Return the memory model.
     *
     * @return \Rubix\Server\Models\Memory
     */
    public function memory() : Memory
    {
        return $this->memory;
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
