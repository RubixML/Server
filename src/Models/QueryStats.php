<?php

namespace Rubix\Server\Models;

use Rubix\Server\Services\SSEChannel;

class QueryStats
{
    /**
     * The server-sent events emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * The number of queries that have been accepted so far.
     *
     * @var int[]
     */
    protected $accepted;

    /**
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(SSEChannel $channel)
    {
        $this->channel = $channel;
    }
}
