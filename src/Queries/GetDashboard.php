<?php

namespace Rubix\Server\Queries;

/**
 * Get Dashboard
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class GetDashboard extends Query
{
    /**
     * Build the query from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self();
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Get Dashboard';
    }
}
