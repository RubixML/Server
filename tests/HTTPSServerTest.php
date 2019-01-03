<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Server;
use Rubix\Server\HTTPSServer;
use Rubix\ML\Classifiers\DummyClassifier;
use Psr\Log\LoggerAwareInterface;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

class HTTPSServerTest extends TestCase
{
    protected $server;

    public function setUp()
    {
        $this->server = new HTTPSServer([
            '/sentiment' => new DummyClassifier(),
        ], [], '127.0.0.1', 8888);
    }

    public function test_build_http_server()
    {
        $this->assertInstanceOf(HTTPSServer::class, $this->server);
        $this->assertInstanceOf(Server::class, $this->server);
        $this->assertInstanceOf(LoggerAwareInterface::class, $this->server);
    }
}
