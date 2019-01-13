<?php

namespace Rubix\Server\Commands;

use InvalidArgumentException;

/**
 * Query Model
 *
 * Return information regarding the model being served.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class QueryModel implements Command
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