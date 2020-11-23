<?php

namespace Rubix\Server\Events;

use Exception;

/**
 * Failure
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
abstract class Failure implements Event
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
}
