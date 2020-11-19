<?php

namespace Rubix\Server\Events;

/**
 * Query Failed
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class QueryFailed extends Failure
{
    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Query Failed';
    }
}
