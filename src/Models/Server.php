<?php

namespace Rubix\Server\Models;

use Rubix\Server\HTTPServer;
use Rubix\Server\Services\EventBus;

class Server
{
    /**
     * The request/response statistics.
     *
     * @var \Rubix\Server\Models\HTTPStats
     */
    protected \Rubix\Server\Models\HTTPStats $httpStats;

    /**
     * The memory model.
     *
     * @var \Rubix\Server\Models\Memory
     */
    protected \Rubix\Server\Models\Memory $memory;

    /**
     * The server info model.
     *
     * @var \Rubix\Server\Models\ProcessInfo
     */
    protected \Rubix\Server\Models\ProcessInfo $info;

    /**
     * The server settings.
     *
     * @var \Rubix\Server\Models\ServerSettings
     */
    protected \Rubix\Server\Models\ServerSettings $settings;

    /**
     * @param \Rubix\Server\HTTPServer $server
     * @param \Rubix\Server\Services\EventBus $eventBus
     */
    public function __construct(HTTPServer $server, EventBus $eventBus)
    {
        $this->httpStats = new HTTPStats();
        $this->memory = new Memory($eventBus);
        $this->info = new ProcessInfo();
        $this->settings = new ServerSettings($server);
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
     * @return \Rubix\Server\Models\ProcessInfo
     */
    public function info() : ProcessInfo
    {
        return $this->info;
    }

    /**
     * Return the server settings model.
     *
     * @return \Rubix\Server\Models\ServerSettings
     */
    public function settings() : ServerSettings
    {
        return $this->settings;
    }

    /**
     * The amount of unutilized memory in bytes.
     *
     * @return int
     */
    public function memoryAvailable() : int
    {
        $memoryLimit = $this->settings->memoryLimit();

        return $memoryLimit === -1
            ? PHP_INT_MAX
            : $memoryLimit - $this->memory->current();
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
            'info' => $this->info->asArray(),
            'settings' => $this->settings->asArray(),
        ];
    }
}
