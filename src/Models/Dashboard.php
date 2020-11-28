<?php

namespace Rubix\Server\Models;

use Rubix\Server\HTTPServer;
use Rubix\Server\Services\SSEChannel;

class Dashboard implements Model
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
     * The timestamp from when the server went up.
     *
     * @var int
     */
    protected $start;

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
        $this->start = time();
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
     * Return the starting timestamp.
     *
     * @return int
     */
    public function start() : int
    {
        return $this->start;
    }

    /**
     * Return the configuration settings model.
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
            'start' => $this->start,
            'configuration' => $this->configuration->asArray(),
        ];
    }
}
