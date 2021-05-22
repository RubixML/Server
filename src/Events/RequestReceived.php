<?php

namespace Rubix\Server\Events;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Request Received
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RequestReceived implements Event
{
    /**
     * The request.
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected \Psr\Http\Message\ServerRequestInterface $request;

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Return the request.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function request() : ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Request Received';
    }
}
