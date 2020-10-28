<?php

namespace Rubix\Server\Tests;

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
        $this->client = new RPCClient('127.0.0.1', 8888, false, [], new Native(), 0.0, 3, 0.3);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(RPCClient::class, $this->client);
    }
}
