<?php

namespace Rubix\Server\Events;

use Exception;

/**
 * Model Query Failed
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ModelQueryFailed extends Failure
{
    /**
     * @param \Exception $exception
     */
    public function __construct(Exception $exception)
    {
        parent::__construct($exception);
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Model Query Failed';
    }
}
