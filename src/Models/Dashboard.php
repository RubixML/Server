<?php

namespace Rubix\Server\Models;

use Rubix\Server\Services\SSEChannel;

class Dashboard
{
    /**
     * The request/response statistics.
     *
     * @var \Rubix\Server\Models\HTTPStats
     */
    protected $httpStats;

    /**
     * The query statistics.
     *
     * @var \Rubix\Server\Models\QueryStats
     */
    protected $queryStats;

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

    /**
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(SSEChannel $channel)
    {
        $this->httpStats = new HTTPStats($channel);
        $this->queryStats = new QueryStats($channel);
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
     * Return the query stats model.
     *
     * @return \Rubix\Server\Models\QueryStats
     */
    public function queryStats() : QueryStats
    {
        return $this->queryStats;
    }

    /**
     * Return the current number of requests handled per second.
     *
     * @return float
     */
    public function responseRate() : float
    {
        return $this->httpStats->numResponses() / $this->uptime();
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
