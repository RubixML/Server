<?php

namespace Rubix\Server\Models;

use Rubix\Server\Services\SSEChannel;

use function memory_get_usage;
use function memory_get_peak_usage;

class Memory extends Model
{
    /**
     * The server-sent events emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * The current memory usage in bytes.
     *
     * @var int
     */
    protected $current;

    /**
     * The peak memory usage in bytes.
     *
     * @var int
     */
    protected $peak;

    /**
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(SSEChannel $channel)
    {
        $this->channel = $channel;
        $this->current = memory_get_usage();
        $this->peak = memory_get_peak_usage();
    }

    /**
     * Update the memory usage.
     *
     * @param int $current
     * @param int $peak
     */
    public function updateUsage(int $current, int $peak) : void
    {
        $this->current = $current;
        $this->peak = $peak;

        $this->channel->emit('memory-usage-updated', [
            'current' => $current,
            'peak' => $peak,
        ]);
    }

    /**
     * Return the current memory usage of the server in bytes.
     *
     * @return int
     */
    public function current() : int
    {
        return $this->current;
    }

    /**
     * Return the peak memory usage of the server in bytes.
     *
     * @return int
     */
    public function peak() : int
    {
        return $this->peak;
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'current' => $this->current,
            'peak' => $this->peak,
        ];
    }
}
