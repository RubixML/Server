<?php

namespace Rubix\Server\Specifications;

use Rubix\Server\Exceptions\ValidationException;

/**
 * Specification
 *
 * @internal
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
abstract class Specification
{
    /**
     * Perform a check of the specification and throw an exception if invalid.
     *
     * @throws \Rubix\Server\Exceptions\ValidationException
     */
    abstract public function check() : void;

    /**
     * Does the specification pass?
     *
     * @return bool
     */
    public function passes() : bool
    {
        try {
            $this->check();

            return true;
        } catch (ValidationException $exception) {
            return false;
        }
    }

    /**
     * Does the specification fail?
     *
     * @return bool
     */
    public function fails() : bool
    {
        return !$this->passes();
    }
}
