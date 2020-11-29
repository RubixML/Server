<?php

namespace Rubix\Server\Models;

use Rubix\Server\Services\SSEChannel;

class Memory extends Model
{
    /**
     * The server-sent events emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(SSEChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Update the memory usage.
     */
    public function updateUsage() : void
    {
        $this->channel->emit('memory-usage-updated', $this->asArray());
    }

    /**
     * Return the current memory usage of the server in bytes.
     *
     * @return int
     */
    public function current() : int
    {
        return memory_get_usage();
    }

    /**
     * Return the peak memory usage of the server in bytes.
     *
     * @return int
     */
    public function peak() : int
    {
        return memory_get_peak_usage();
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'current' => $this->current(),
            'peak' => $this->peak(),
        ];
    }
}
