<?php

namespace Rubix\Server\Models;

use Rubix\Server\Services\SSEChannel;

class Dashboard implements Model
{
    /**
     * The request/response statistics.
     *
     * @var \Rubix\Server\Models\HTTPStats
     */
    protected $httpStats;

    /**
     * The query log model.
     *
     * @var \Rubix\Server\Models\QueryLog
     */
    protected $queryLog;

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
        $this->queryLog = new QueryLog($channel);
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
     * Return the query log model.
     *
     * @return \Rubix\Server\Models\QueryLog
     */
    public function queryLog() : QueryLog
    {
        return $this->queryLog;
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

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'requests' => $this->httpStats->asArray(),
            'queries' => $this->queryLog->asArray(),
            'memory' => $this->memory->asArray(),
            'uptime' => $this->uptime(),
        ];
    }
}
