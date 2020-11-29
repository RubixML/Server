<?php

namespace Rubix\Server\Models;

use Rubix\Server\HTTPServer;
use Rubix\Server\Services\SSEChannel;

class Dashboard extends Model
{
    /**
     * The request/response statistics.
     *
     * @var \Rubix\Server\Models\HTTPStats
     */
    protected $httpStats;

    /**
     * The memory model.
     *
     * @var \Rubix\Server\Models\Memory
     */
    protected $memory;

    /**
     * The server info model.
     *
     * @var \Rubix\Server\Models\ServerInfo
     */
    protected $serverInfo;

    /**
     * The server configuration settings.
     *
     * @var \Rubix\Server\Models\Configuration
     */
    protected $configuration;

    /**
     * @param \Rubix\Server\HTTPServer $server
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(HTTPServer $server, SSEChannel $channel)
    {
        $this->httpStats = new HTTPStats($channel);
        $this->memory = new Memory($channel);
        $this->serverInfo = new ServerInfo();
        $this->configuration = new Configuration($server);
    }

    /**
     * Return the HTTP stats model.
     *
     * @return \Rubix\Server\Models\HTTPStats
     */
    public function httpStats() : HTTPStats
    {
        return $this->httpStats;
    }

    /**
     * Return the memory model.
     *
     * @return \Rubix\Server\Models\Memory
     */
    public function memory() : Memory
    {
        return $this->memory;
    }

    /**
     * Return the server info model.
     *
     * @return \Rubix\Server\Models\ServerInfo
     */
    public function serverInfo() : ServerInfo
    {
        return $this->serverInfo;
    }

    /**
     * Return the configuration model.
     *
     * @return \Rubix\Server\Models\Configuration
     */
    public function configuration() : Configuration
    {
        return $this->configuration;
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'httpStats' => $this->httpStats->asArray(),
            'memory' => $this->memory->asArray(),
            'info' => $this->serverInfo->asArray(),
            'configuration' => $this->configuration->asArray(),
        ];
    }
}
