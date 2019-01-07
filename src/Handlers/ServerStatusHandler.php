<?php

namespace Rubix\Server\Handlers;

use Rubix\Server\Server;
use Rubix\Server\Commands\ServerStatus;

class ServerStatusHandler implements Handler
{
    /**
     * The server instance.
     * 
     * @var \Rubix\Server\Server
     */
    protected $server;

    /**
     * @param  \Rubix\Server\Server  $server
     * @return void
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Handle the command.
     * 
     * @param  \Rubix\Server\Commands\ServerStatus  $command
     * @return array
     */
    public function handle(ServerStatus $command) : array
    {
        $n = $this->server->requests();
        $uptime = $this->server->uptime();
        
        $current = memory_get_usage();
        $peak = memory_get_peak_usage();

        return [
            'requests' => [
                'count' => $n,
                'requests_min' => round($n / ($uptime / 60), 2),
                'requests_sec' => round($n / $uptime, 2),
            ],
            'memory_usage' => [
                'current' => round($current / (1024 ** 2), 1),
                'peak' => round($peak / (1024 ** 2), 1),
            ],
            'uptime' => $uptime,
        ];
    }
}