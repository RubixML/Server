<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Server;
use Rubix\Server\ZeroMQServer;
use Rubix\Server\Serializers\Native;
use Rubix\ML\Classifiers\DummyClassifier;
use Psr\Log\LoggerAwareInterface;
use PHPUnit\Framework\TestCase;

class ZeroMQServerTest extends TestCase
{
    protected $server;

    public function setUp()
    {
        $this->server = new ZeroMQServer(new DummyClassifier(), '127.0.0.1', 5555, 'tcp', new Native());
    }

    public function test_build_server()
    {
        $this->assertInstanceOf(ZeroMQServer::class, $this->server);
        $this->assertInstanceOf(Server::class, $this->server);
        $this->assertInstanceOf(LoggerAwareInterface::class, $this->server);
    }
}
