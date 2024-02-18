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
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Return the request.
     *
     * @return ServerRequestInterface
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
