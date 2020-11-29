<?php

namespace Rubix\Server\Payloads;

abstract class Payload
{
    /**
     * Return the payload as an associative array.
     *
     * @return mixed[]
     */
    abstract public function asArray() : array;
}
