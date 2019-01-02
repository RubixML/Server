<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Server;
use Rubix\Server\HTTPServer;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

class HTTPServerTest extends TestCase
{
    protected $server;

    public function setUp()
    {
        $this->server = new HTTPServer([
            '/sentiment' => new DummyClassifier(),
        ], '127.0.0.1', 8888);
    }

    public function test_build_http_server()
    {
        $this->assertInstanceOf(HTTPServer::class, $this->server);
        $this->assertInstanceOf(Server::class, $this->server);
    }
}
