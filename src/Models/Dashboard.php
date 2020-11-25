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
     * Return the starting timestamp.
     *
     * @return int
     */
    public function start() : int
    {
        return $this->start;
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'http_stats' => $this->httpStats->asArray(),
            'query_log' => $this->queryLog->asArray(),
            'start' => $this->start,
        ];
    }
}
