<?php

namespace Rubix\Server\Exceptions;

use Rubix\Server\Queries\Query;

class HandlerNotFound extends RuntimeException
{
    /**
     * @param \Rubix\Server\Queries\Query $query
     */
    public function __construct(Query $query)
    {
        parent::__construct("The $query query is not supported.");
    }
}
