<?php

namespace Rubix\Server\Models;

use Rubix\Server\Queries\Query;
use Rubix\Server\Services\SSEChannel;

class QueryLog
{
    /**
     * The server-sent events emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * The number of queries that have been accepted so far for each query.
     *
     * @var int[]
     */
    protected $accepted = [
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
     * Record a query in the query log.
     *
     * @param \Rubix\Server\Queries\Query $query
     */
    public function record(Query $query) : void
    {
        $name = (string) $query;

        if (isset($this->accepted[$name])) {
            ++$this->accepted[$name];
        } else {
            $this->accepted[$name] = 1;
        }

        $this->channel->emit('query-accepted', [
            'name' => $name,
        ]);
    }

    /**
     * Return the a list of queries and their counts.
     *
     * @return int[]
     */
    public function accepted() : array
    {
        return $this->accepted;
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'accepted' => $this->accepted,
        ];
    }
}
