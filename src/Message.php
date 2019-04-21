<?php

namespace Rubix\Server;

use JsonSerializable;

abstract class Message implements JsonSerializable
{
    /**
     * Build the message from an associative array of data.
     *
     * @param array $data
     * @return self
     */
    abstract public static function fromArray(array $data);

    /**
     * Return the message as an array.
     *
     * @return array
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
