<?php

namespace Rubix\Server\Exceptions;

use const Rubix\Server\Http\BAD_REQUEST;

class ValidationException extends RubixServerException
{
    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message, BAD_REQUEST);
    }
}
