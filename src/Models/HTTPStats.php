<?php

namespace Rubix\Server\Models;

use Rubix\Server\Services\SSEChannel;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class HTTPStats implements Model
{
    /**
     * The server-sent events emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * The number of requests received by the server.
     *
     * @var int
     */
    protected $numRequests = 0;

    /**
     * The number of successful requests handled by the server.
     *
     * @var int
     */
    protected $numSuccessful = 0;

    /**
     * The number of rejected requests.
     *
     * @var int
     */
    protected $numRejected = 0;

    /**
     * The number of failed requests.
     *
     * @var int
     */
    protected $numFailed = 0;

    /**
     * The number of bytes that have been received by the server.
     *
     * @var int
     */
    protected $bytesReceived = 0;

    /**
     * The number of bytes that have been sent by the server.
     *
     * @var int
     */
    protected $bytesSent = 0;

    /**
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(SSEChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Record an HTTP request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function recordRequest(ServerRequestInterface $request) : void
    {
        ++$this->numRequests;

        if ($request->hasHeader('Content-Length')) {
            $size = (int) $request->getHeaderLine('Content-Length');

            $this->bytesReceived += $size;
        } else {
            $size = null;
        }

        $this->channel->emit('request-recorded', [
            'size' => $size,
        ]);
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

        $this->channel->emit('response-recorded', [
            'code' => $code,
            'size' => $size,
        ]);
    }

    /**
     * Return the number of requests received by the server.
     *
     * @return int
     */
    public function numRequests() : int
    {
        return $this->numRequests;
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
            'requests' => $this->numRequests,
            'responses' => [
                'successful' => $this->numSuccessful,
                'rejected' => $this->numRejected,
                'failed' => $this->numFailed,
            ],
            'transferred' => [
                'received' => $this->bytesReceived,
                'sent' => $this->bytesSent,
            ],
        ];
    }
}
