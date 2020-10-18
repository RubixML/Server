<?php

namespace Rubix\Server\Handlers;

use Rubix\Server\Server;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Responses\ServerStatusResponse;

class ServerStatusHandler implements Handler
{
    protected const MINUTE = 60;

    protected const MEGABYTE = 1048576;

    /**
     * The verbose server instance.
     *
     * @var \Rubix\Server\Server
     */
    protected $server;

    /**
     * @param \Rubix\Server\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Commands\ServerStatus $command
     * @return \Rubix\Server\Responses\ServerStatusResponse
     */
    public function handle(ServerStatus $command) : ServerStatusResponse
    {
        $uptime = $this->server->uptime() ?: 1;

        $n = $this->server->requests();

        $requests = [
            'count' => $n,
            'per_minute' => round($n / ($uptime / self::MINUTE), 2),
        ];

        $memoryUsage = [
            'current' => round(memory_get_usage() / self::MEGABYTE, 1),
            'peak' => round(memory_get_peak_usage() / self::MEGABYTE, 1),
        ];

        return new ServerStatusResponse($requests, $memoryUsage, $uptime);
    }
}
