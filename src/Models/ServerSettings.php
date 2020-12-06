<?php

namespace Rubix\Server\Models;

use Rubix\Server\HTTPServer;
use React\Http\Io\IniUtil;

class ServerSettings extends Model
{
    /**
     * The server instance.
     *
     * @var \Rubix\Server\HTTPServer
     */
    protected $server;

    /**
     * The maximum number of bytes that the server can consume.
     *
     * @var int
     */
    protected $memoryLimit;

    /**
     * The maximum size of a request body in bytes.
     *
     * @var int
     */
    protected $postMaxSize;

    /**
     * @param \Rubix\Server\HTTPServer $server
     */
    public function __construct(HTTPServer $server)
    {
        $this->server = $server;
        $this->memoryLimit = IniUtil::iniSizeToBytes((string) ini_get('memory_limit'));
        $this->postMaxSize = IniUtil::iniSizeToBytes((string) ini_get('post_max_size'));
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
     * Return the memory limit in bytes.
     *
     * @return int
     */
    public function memoryLimit() : int
    {
        return $this->memoryLimit;
    }

    /**
     * Return the maximum body size of a request in bytes.
     *
     * @return int
     */
    public function postMaxSize() : int
    {
        return $this->postMaxSize;
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
            'sseReconnectBuffer' => $this->server->sseReconnectBuffer(),
            'memoryLimit' => $this->memoryLimit,
            'postMaxSize' => $this->postMaxSize,
        ];
    }
}
