<?php

namespace Rubix\Server\Exceptions;

use Rubix\Server\Commands\Command;

use const Rubix\Server\Http\BAD_REQUEST;

class HandlerNotFound extends RuntimeException
{
    /**
     * @param \Rubix\Server\Commands\Command $command
     */
    public function __construct(Command $command)
    {
        parent::__construct("The $command command is not supported.", BAD_REQUEST);
    }
}
