<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Server;
use Rubix\Server\RESTServer;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use PHPUnit\Framework\TestCase;

/**
 * @group Servers
 * @covers \Rubix\Server\RESTServer
 */
class RESTServerTest extends TestCase
{
    /**
     * @var \Rubix\Server\RESTServer
     */
    protected $server;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->server = new RESTServer('127.0.0.1', 8888, null, [
            new SharedTokenAuthenticator(['secret']),
        ]);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(RESTServer::class, $this->server);
        $this->assertInstanceOf(Server::class, $this->server);
    }
}
