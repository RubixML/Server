<?php

namespace Rubix\Server\Events;

use Exception;

/**
 * Query Failed
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class QueryFailed extends Event
{
    /**
     * The exception.
     *
     * @var \Exception
     */
    protected $exception;

    /**
     * @param \Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Return the exception.
     *
     * @return \Exception
     */
    public function exception() : Exception
    {
        return $this->exception;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Query Rejected';
    }
}
