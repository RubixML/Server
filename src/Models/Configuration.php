<?php

namespace Rubix\Server\Models;

class Configuration implements Model
{
    protected const UNKNOWN = 'unknown';

    /**
     * Return the memory limit.
     *
     * @return string
     */
    public function memoryLimit() : string
    {
        return ini_get('memory_limit') ?: self::UNKNOWN;
    }

    /**
     * Return the maximum body size of a request.
     *
     * @return string
     */
    public function postMaxSize() : string
    {
        return ini_get('post_max_size') ?: self::UNKNOWN;
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'memoryLimit' => $this->memoryLimit(),
            'postMaxSize' => $this->postMaxSize(),
        ];
    }
}
