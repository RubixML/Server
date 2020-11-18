<?php

namespace Rubix\Server\Events;

use Rubix\Server\Queries\Query;

/**
 * Query Accepted
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class QueryAccepted extends Event
{
    /**
     * The query.
     *
     * @var \Rubix\Server\Queries\Query
     */
    protected $query;

    /**
     * @param \Rubix\Server\Queries\Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Return the query.
     *
     * @return \Rubix\Server\Queries\Query
     */
    public function query() : Query
    {
        return $this->query;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Query Accepted';
    }
}
