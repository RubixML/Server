<?php

namespace Rubix\Server\Events;

use Psr\Http\Message\ResponseInterface;

/**
 * Request Received
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ResponseSent implements Event
{
    /**
     * The response.
     *
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Return the response.
     *
     * @return ResponseInterface
     */
    public function response() : ResponseInterface
    {
        return $this->response;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Response Sent';
    }
}
