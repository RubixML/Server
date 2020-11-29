<?php

namespace Rubix\Server\Models;

use Rubix\Server\HTTPServer;

class Configuration extends Model
{
    protected const UNKNOWN = 'unknown';

    /**
     * The server instance.
     *
     * @var \Rubix\Server\HTTPServer
     */
    protected $server;

    /**
     * @param \Rubix\Server\HTTPServer $server
     */
    public function __construct(HTTPServer $server)
    {
        $this->server = $server;
    }

    /**
     * Return the host address.
     *
     * @return string
     */
    public function host() : string
    {
        return $this->server->host();
    }

    /**
     * Return the TCP port number.
     *
     * @return int
     */
    public function port() : int
    {
        return $this->server->port();
    }

    /**
     * Return the maximum number of concurrent requests.
     *
     * @return int
     */
    public function maxConcurrentRequests() : int
    {
        return $this->server->maxConcurrentRequests();
    }

    /**
     * Return the size of the SSE reconnect buffer.
     *
     * @return int
     */
    public function sseReconnectBuffer() : int
    {
        return $this->server->sseReconnectBuffer();
    }

    /**
     * Return the memory limit.
     *
     * @return string
     */
    public function memoryLimit() : string
    {
        return ini_get('memory_limit') ?: self::UNKNOWN;
    }

    /**
     * Return the maximum body size of a request.
     *
     * @return string
     */
    public function postMaxSize() : string
    {
        return ini_get('post_max_size') ?: self::UNKNOWN;
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'host' => $this->server->host(),
            'port' => $this->server->port(),
            'maxConcurrentRequests' => $this->server->maxConcurrentRequests(),
            'memoryLimit' => $this->memoryLimit(),
            'postMaxSize' => $this->postMaxSize(),
            'sseReconnectBuffer' => $this->server->sseReconnectBuffer(),
        ];
    }
}
