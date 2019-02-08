<?php

namespace Rubix\Server\Handlers;

use Rubix\Server\Server;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Responses\ServerStatusResponse;

class ServerStatusHandler implements Handler
{
    /**
     * The server instance.
     *
     * @var \Rubix\Server\Server
     */
    protected $server;

    /**
     * @param \Rubix\Server\Server $server
     * @return void
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
            'requestsMin' => round($n / ($uptime / 60), 2),
        ];
        
        $memoryUsage = [
            'current' => round(memory_get_usage() / (1024 ** 2), 1),
            'peak' => round(memory_get_peak_usage() / (1024 ** 2), 1),
        ];

        return new ServerStatusResponse($requests, $memoryUsage, $uptime);
    }
}
