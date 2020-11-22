<?php

namespace Rubix\Server;

use JSONSerializable;

use function get_class;

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
     * Return the message as an associative array.
     *
     * @return mixed[]
     */
    abstract public function asArray() : array;

    /**
     * Return the payload for JSON serialization.
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
