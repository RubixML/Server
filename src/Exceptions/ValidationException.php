<?php

namespace Rubix\Server\Exceptions;

use const Rubix\Server\Http\UNPROCESSABLE_ENTITY;

class ValidationException extends RuntimeException
{
    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message, UNPROCESSABLE_ENTITY);
    }
}
