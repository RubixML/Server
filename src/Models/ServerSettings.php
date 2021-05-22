<?php

namespace Rubix\Server\Models;

use Rubix\Server\HTTPServer;

class ServerSettings
{
    /**
     * The server instance.
     *
     * @var \Rubix\Server\HTTPServer
     */
    protected \Rubix\Server\HTTPServer $server;

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
     * Is transport layer security (TLS) enabled?
     *
     * @return bool
     */
    public function tls() : bool
    {
        return $this->server->tls();
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
     * Return the memory limit in bytes.
     *
     * @return int
     */
    public function memoryLimit() : int
    {
        return $this->server->memoryLimit();
    }

    /**
     * Return the maximum body size of a request in bytes.
     *
     * @return int
     */
    public function postMaxSize() : int
    {
        return $this->server->postMaxSize();
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'host' => $this->host(),
            'port' => $this->port(),
            'tls' => $this->tls(),
            'maxConcurrentRequests' => $this->maxConcurrentRequests(),
            'memoryLimit' => $this->memoryLimit(),
            'postMaxSize' => $this->postMaxSize(),
        ];
    }
}
