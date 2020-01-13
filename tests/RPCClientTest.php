<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Client;
use Rubix\Server\RPCClient;
use Rubix\Server\Serializers\Native;
use PHPUnit\Framework\TestCase;

/**
 * @group Clients
 * @covers \Rubix\Server\RPCClient
 */
class RPCClientTest extends TestCase
{
    /**
     * @var \Rubix\Server\RPCClient
     */
    protected $client;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->client = new RPCClient('127.0.0.1', 8888, false, [], new Native());
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(RPCClient::class, $this->client);
        $this->assertInstanceOf(Client::class, $this->client);
    }
}
