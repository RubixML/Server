<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Server;
use Rubix\Server\RPCServer;
use Rubix\Server\Serializers\Native;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use Psr\Log\LoggerAwareInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group Servers
 * @covers \Rubix\Server\RPCServer
 */
class RPCServerTest extends TestCase
{
    /**
     * @var \Rubix\Server\RPCServer
     */
    protected $server;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->server = new RPCServer('127.0.0.1', 8080, null, [
            new SharedTokenAuthenticator('secret'),
        ], new Native());
    }

    protected function assertPreConditions() : void
    {
        $this->assertEquals(0, $this->server->requests());
        $this->assertEquals(0, $this->server->uptime());
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(RPCServer::class, $this->server);
        $this->assertInstanceOf(Server::class, $this->server);
        $this->assertInstanceOf(LoggerAwareInterface::class, $this->server);
    }
}
