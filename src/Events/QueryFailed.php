<?php

namespace Rubix\Server\Events;

use Rubix\Server\Queries\Query;
use Exception;

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
     * The query.
     *
     * @var \Rubix\Server\Queries\Query
     */
    protected $query;

    /**
     * @param \Rubix\Server\Queries\Query $query
     * @param \Exception $exception
     */
    public function __construct(Query $query, Exception $exception)
    {
        $this->query = $query;

        parent::__construct($exception);
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
        return 'Query Failed';
    }
}
