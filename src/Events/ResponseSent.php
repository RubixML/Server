<?php

namespace Rubix\Server\Events;

use Psr\Http\Message\ResponseInterface;

class ResponseSent extends Event
{
    /**
     * The response.
     * 
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Return the response.
     * 
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function response() : ResponseInterface
    {
        return $this->response;
    }
}