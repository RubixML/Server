<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Server;
use Rubix\Server\RESTServer;
use Rubix\ML\Classifiers\DummyClassifier;
use Psr\Log\LoggerAwareInterface;
use PHPUnit\Framework\TestCase;

class RESTServerTest extends TestCase
{
    protected $server;

    public function setUp()
    {
        $this->server = new RESTServer(new DummyClassifier(), '127.0.0.1', 8888, [], null);
    }

    public function test_build_server()
    {
        $this->assertInstanceOf(RESTServer::class, $this->server);
        $this->assertInstanceOf(Server::class, $this->server);
        $this->assertInstanceOf(LoggerAwareInterface::class, $this->server);
    }
}
