<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Server;
use Rubix\Server\HTTPServer;
use Rubix\Server\HTTP\Middleware\SharedTokenAuthenticator;
use PHPUnit\Framework\TestCase;

/**
 * @group Servers
 * @covers \Rubix\Server\HTTPServer
 */
class HTTPServerTest extends TestCase
{
    /**
     * @var HTTPServer
     */
    protected $server;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->server = new HTTPServer('127.0.0.1', 8888, null, [
            new SharedTokenAuthenticator(['secret']),
        ], 50);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(HTTPServer::class, $this->server);
        $this->assertInstanceOf(Server::class, $this->server);
    }
}
