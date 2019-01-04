<?php

namespace Rubix\Server\Controllers;

use Rubix\Server\RESTServer;
use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Status implements Controller
{
    const HEADERS = [
        'Content-Type' => 'text/json',
    ];

    /**
     * The REST server instance.
     * 
     * @var \Rubix\Server\RESTServer
     */
    protected $server;

    /**
     * @param  \Rubix\Server\RESTServer  $server
     * @return void
     */
    public function __construct(RESTServer $server)
    {
        $this->server = $server;
    }

    /**
     * Handle the request.
     * 
     * @param  Request  $request
     * @param  array  $params
     * @return Response
     */
    public function handle(Request $request, array $params) : Response
    {
        $n = $this->server->requests();
        $uptime = $this->server->uptime();
        
        $current = memory_get_usage();
        $peak = memory_get_peak_usage();

        return new ReactResponse(200, self::HEADERS, json_encode([
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
        ]));
    }
}