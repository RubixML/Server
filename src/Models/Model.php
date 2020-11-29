<?php

namespace Rubix\Server\Models;

abstract class Model
{
    /**
     * Return the model properties as an associative array.
     *
     * @return mixed[]
     */
    abstract public function asArray() : array;
}
