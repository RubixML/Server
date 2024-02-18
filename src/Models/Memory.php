<?php

namespace Rubix\Server\Models;

use Rubix\Server\Services\EventBus;
use Rubix\Server\Events\MemoryUsageUpdated;

use function memory_get_usage;
use function memory_get_peak_usage;

class Memory
{
    /**
     * The event bus.
     *
     * @var EventBus
     */
    protected EventBus $eventBus;

    /**
     * @param EventBus $eventBus
     */
    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * Update the memory usage.
     */
    public function updateUsage() : void
    {
        $this->eventBus->dispatch(new MemoryUsageUpdated($this));
    }

    /**
     * Return the current memory usage of the server in bytes.
     *
     * @return int
     */
    public function current() : int
    {
        return memory_get_usage(true);
    }

    /**
     * Return the peak memory usage of the server in bytes.
     *
     * @return int
     */
    public function peak() : int
    {
        return memory_get_peak_usage(true);
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
