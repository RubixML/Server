<?php

namespace Rubix\Server\Events;

interface Emittable
{
    /**
     * The name of the event.
     *
     * @return string
     */
    public function name() : string;

    /**
     * Return the event as an associative array of data.
     *
     * @return mixed[]
     */
    public function asArray() : array;
}
