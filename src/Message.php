<?php

namespace Rubix\Server;

use InvalidArgumentException;
use JsonSerializable;

abstract class Message implements JsonSerializable
{
    /**
     * Build the message from an associative array of data.
     * 
     * @param  array  $data
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

    /**
     * Magic getters to access the payload properties of the message.
     * 
     * @param  string  $property
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function __get(string $property)
    {
        $properties = $this->asArray();

        if (!isset($properties[$property])) {
            throw new InvalidArgumentException('Property'
                . " '$property' could not be found.");
        }

        return $properties[$property];
    }
}