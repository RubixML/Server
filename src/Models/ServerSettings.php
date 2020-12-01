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
     * Return the memory limit in bytes.
     *
     * @return int
     */
    public function memoryLimit() : int
    {
        return IniUtil::iniSizeToBytes((string) ini_get('memory_limit'));
    }

    /**
     * Return the maximum body size of a request in bytes.
     *
     * @return int
     */
    public function postMaxSize() : int
    {
        return IniUtil::iniSizeToBytes((string) ini_get('post_max_size'));
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
