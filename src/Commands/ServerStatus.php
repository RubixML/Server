<?php

namespace Rubix\Server\Commands;

use InvalidArgumentException;

class ServerStatus implements Command
{
    /**
     * Return the payload.
     * 
     * @return array
     */
    public function payload() : array
    {
        return [
            //
        ];
    }
}