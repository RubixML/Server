<?php

namespace Rubix\Server\Events;

/**
 * Request Failed
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RequestFailed extends Failure
{
    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Request Failed';
    }
}
