<?php

namespace Rubix\Server\Models;

use Rubix\Server\Queries\Query;
use Rubix\Server\Services\SSEChannel;

class QueryLog implements Model
{
    /**
     * The server-sent events (SSE) emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * The log entries for each query.
     *
     * @var array[]
     */
    protected $log = [
        //
    ];

    /**
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(SSEChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Record a fulfilled query in the query log.
     *
     * @param \Rubix\Server\Queries\Query $query
     */
    public function recordFulfilled(Query $query) : void
    {
        $name = (string) $query;

        if (isset($this->log[$name])) {
            ++$this->log[$name]['fulfilled'];
        } else {
            $this->log[$name]['fulfilled'] = 1;
            $this->log[$name]['failed'] = 0;
        }

        $this->channel->emit('query-fulfilled', [
            'name' => $name,
        ]);
    }

    /**
     * Record a failed query in the query log.
     *
     * @param \Rubix\Server\Queries\Query $query
     */
    public function recordFailed(Query $query) : void
    {
        $name = (string) $query;

        if (isset($this->log[$name])) {
            ++$this->log[$name]['failed'];
        } else {
            $this->log[$name]['fulfilled'] = 0;
            $this->log[$name]['failed'] = 1;
        }

        $this->channel->emit('query-failed', [
            'name' => $name,
        ]);
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return $this->log;
    }
}
