<?php

namespace Rubix\Server\Models;

class Memory
{
    protected const MEGA_BYTE = 1000000;

    /**
     * Return the current memory usage of the server in mega bytes (MB).
     *
     * @return float
     */
    public function usage() : float
    {
        return memory_get_usage() / self::MEGA_BYTE;
    }

    /**
     * Return the peak memory usage of the server in mega bytes (MB).
     *
     * @return float
     */
    public function peak() : float
    {
        return memory_get_peak_usage() / self::MEGA_BYTE;
    }
}
