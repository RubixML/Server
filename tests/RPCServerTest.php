<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Server;
use Rubix\Server\RPCServer;
use Rubix\Server\Serializers\Native;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use Psr\Log\LoggerAwareInterface;
use PHPUnit\Framework\TestCase;

class RPCServerTest extends TestCase
{
    protected $server;

    public function setUp()
    {
        $this->server = new RPCServer('127.0.0.1', 8080, null, [
            new SharedTokenAuthenticator('secret'),
        ], new Native());
    }

    public function test_build_server()
    {
        $this->assertInstanceOf(RPCServer::class, $this->server);
        $this->assertInstanceOf(Server::class, $this->server);
        $this->assertInstanceOf(LoggerAwareInterface::class, $this->server);

        $this->assertEquals(0, $this->server->requests());
        $this->assertEquals(0, $this->server->uptime());
    }
}
