<?php

namespace Rubix\Server\Events;

/**
 * Shutting Down
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ShuttingDown implements Event
{
    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Shutting Down';
    }
}
