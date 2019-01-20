<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Client;
use Rubix\Server\ZMQClient;
use Rubix\Server\Serializers\Native;
use PHPUnit\Framework\TestCase;

class ZMQClientTest extends TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = new ZMQClient('127.0.0.1', 5555, 'tcp', new Native());
    }

    public function test_build_server()
    {
        $this->assertInstanceOf(ZMQClient::class, $this->client);
        $this->assertInstanceOf(Client::class, $this->client);
    }
}
