<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Client;
use Rubix\Server\RPCClient;
use Rubix\Server\Serializers\Native;
use PHPUnit\Framework\TestCase;

class RPCClientTest extends TestCase
{
    /**
     * @var \Rubix\Server\RPCClient
     */
    protected $client;

    public function setUp() : void
    {
        $this->client = new RPCClient('127.0.0.1', 8888, false, [], new Native());
    }

    public function test_build_client() : void
    {
        $this->assertInstanceOf(RPCClient::class, $this->client);
        $this->assertInstanceOf(Client::class, $this->client);
    }
}
