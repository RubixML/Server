<?php

namespace Rubix\Server\Events;

use Rubix\Server\Models\Memory;

/**
 * Memory Usage Updated
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class MemoryUsageUpdated implements Event
{
    /**
     * The memory model.
     *
     * @var Memory
     */
    protected Memory $memory;

    /**
     * @param Memory $memory
     */
    public function __construct(Memory $memory)
    {
        $this->memory = $memory;
    }

    /**
     * Return the memory model.
     *
     * @return Memory
     */
    public function memory() : Memory
    {
        return $this->memory;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Memory Usage Updated';
    }
}
