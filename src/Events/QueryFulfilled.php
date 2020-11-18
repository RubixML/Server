<?php

namespace Rubix\Server\Events;

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
     * The payload.
     *
     * @var \Rubix\Server\Payloads\Payload
     */
    protected $payload;

    /**
     * @param \Rubix\Server\Payloads\Payload $payload
     */
    public function __construct(Payload $payload)
    {
        $this->payload = $payload;
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
