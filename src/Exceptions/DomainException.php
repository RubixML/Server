<?php

namespace Rubix\Server\Exceptions;

use Exception;

use const Rubix\Server\Http\INTERNAL_SERVER_ERROR;

class DomainException extends RubixServerException
{
    /**
     * @param \Exception $previous
     */
    public function __construct(Exception $previous)
    {
        $message = 'Error emitted from the domain model.';

        parent::__construct($message, INTERNAL_SERVER_ERROR, $previous);
    }
}
