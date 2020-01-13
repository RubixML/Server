<?php

namespace Rubix\Server;

use JSONSerializable;

abstract class Message implements JSONSerializable
{
    /**
     * Build the message from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    abstract public static function fromArray(array $data);

    /**
     * Return the message as an array.
     *
     * @return mixed[]
     */
    abstract public function asArray() : array;

    /**
     * Return the payload for json serialization.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'name' => get_class($this),
            'data' => $this->asArray(),
        ];
    }
}
