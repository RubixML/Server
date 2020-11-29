<?php

namespace Rubix\Server\Queries;

use Stringable;

abstract class Query implements Stringable
{
    /**
     * Build the query from an associative array.
     *
     * @param mixed[] $data
     * @return self
     */
    abstract public static function fromArray(array $data);
}
