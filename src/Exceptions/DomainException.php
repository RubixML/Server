<?php

namespace Rubix\Server\Exceptions;

use Exception;

use const Rubix\Server\Http\INTERNAL_SERVER_ERROR;

class DomainException extends RuntimeException
{
    /**
     * @param \Exception $exception
     */
    public function __construct(Exception $exception)
    {
        parent::__construct($exception->getMessage(), INTERNAL_SERVER_ERROR, $exception);
    }
}
