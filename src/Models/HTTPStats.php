<?php

namespace Rubix\Server\Models;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class HTTPStats
{
    /**
     * The number of successful requests handled by the server.
     *
     * @var int
     */
    protected int $numSuccessful = 0;

    /**
     * The number of rejected requests.
     *
     * @var int
     */
    protected int $numRejected = 0;

    /**
     * The number of failed requests.
     *
     * @var int
     */
    protected int $numFailed = 0;

    /**
     * The number of bytes that have been received by the server.
     *
     * @var int
     */
    protected int $bytesReceived = 0;

    /**
     * The number of bytes that have been sent by the server.
     *
     * @var int
     */
    protected int $bytesSent = 0;

    /**
     * Record an HTTP request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function recordRequest(ServerRequestInterface $request) : void
    {
        if ($request->hasHeader('Content-Length')) {
            $size = (int) $request->getHeaderLine('Content-Length');

            $this->bytesReceived += $size;
        }
    }

    /**
     * Record an HTTP response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function recordResponse(ResponseInterface $response) : void
    {
        $code = $response->getStatusCode();

        if ($code >= 100 and $code < 400) {
            ++$this->numSuccessful;
        } elseif ($code >= 400 and $code < 500) {
            ++$this->numRejected;
        } elseif ($code >= 500) {
            ++$this->numFailed;
        }

        $size = $response->getBody()->getSize();

        if ($size) {
            $this->bytesSent += $size;
        }
    }

    /**
     * Return the number of successful requests handled by the server.
     *
     * @return int
     */
    public function numSuccessful() : int
    {
        return $this->numSuccessful;
    }

    /**
     * Return the number of rejected requests.
     *
     * @return int
     */
    public function numRejected() : int
    {
        return $this->numRejected;
    }

    /**
     * Return the number of failed requests.
     *
     * @return int
     */
    public function numFailed() : int
    {
        return $this->numFailed;
    }

    /**
     * Return the number bytes received so far.
     *
     * @return int
     */
    public function bytesReceived() : int
    {
        return $this->bytesReceived;
    }

    /**
     * Return the number bytes sent so far.
     *
     * @return int
     */
    public function bytesSent() : int
    {
        return $this->bytesSent;
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'requests' => [
                'successful' => $this->numSuccessful,
                'rejected' => $this->numRejected,
                'failed' => $this->numFailed,
            ],
            'transfers' => [
                'received' => $this->bytesReceived,
                'sent' => $this->bytesSent,
            ],
        ];
    }
}
