<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Client;
use Rubix\Server\ZeroMQClient;
use Rubix\Server\Serializers\Native;
use PHPUnit\Framework\TestCase;

class ZeroMQClientTest extends TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = new ZeroMQClient('127.0.0.1', 5555, 'tcp', new Native());
    }

    public function test_build_server()
    {
        $this->assertInstanceOf(ZeroMQClient::class, $this->client);
        $this->assertInstanceOf(Client::class, $this->client);
    }
}
