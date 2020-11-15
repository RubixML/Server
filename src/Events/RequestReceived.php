<?php

namespace Rubix\Server\Events;

use Psr\Http\Message\RequestInterface;

class RequestReceived extends Event
{
    /**
     * The request.
     * 
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Return the request.
     * 
     * @return \Psr\Http\Message\RequestInterface
     */
    public function request() : RequestInterface
    {
        return $this->request;
    }
}