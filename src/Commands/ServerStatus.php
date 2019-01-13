<?php

namespace Rubix\Server\Commands;

use InvalidArgumentException;

/**
 * Server Status
 *
 * Query the status of the server.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
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