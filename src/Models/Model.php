<?php

namespace Rubix\Server\Models;

interface Model
{
    /**
     * Return the model properties as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array;
}
