<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\JSON;
use Rubix\Server\Models\Server;
use Rubix\Server\HTTP\Responses\Success;
use Psr\Http\Message\ServerRequestInterface;

class ServerController extends JSONController
{
    /**
     * The server model.
     *
     * @var \Rubix\Server\Models\Server
     */
    protected \Rubix\Server\Models\Server $server;

    /**
     * @param \Rubix\Server\Models\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/server' => [
                'GET' => [$this, 'getServer'],
            ],
        ];
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function getServer(ServerRequestInterface $request)
    {
        return new Success(self::DEFAULT_HEADERS, JSON::encode([
            'data' => [
                'server' => $this->server->asArray(),
            ],
        ]));
    }
}
