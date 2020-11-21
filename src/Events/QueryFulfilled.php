<?php

namespace Rubix\Server\Events;

use Rubix\Server\Queries\Query;
use Rubix\Server\Payloads\Payload;

/**
 * Query Fulfilled
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class QueryFulfilled extends Event
{
    /**
     * The query.
     *
     * @var \Rubix\Server\Queries\Query
     */
    protected $query;

    /**
     * The payload.
     *
     * @var \Rubix\Server\Payloads\Payload
     */
    protected $payload;

    /**
     * @param \Rubix\Server\Queries\Query $query
     * @param \Rubix\Server\Payloads\Payload $payload
     */
    public function __construct(Query $query, Payload $payload)
    {
        $this->query = $query;
        $this->payload = $payload;
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
     * Return the payload.
     *
     * @return \Rubix\Server\Payloads\Payload
     */
    public function payload() : Payload
    {
        return $this->payload;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Query Fulfilled';
    }
}
