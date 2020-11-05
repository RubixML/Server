<?php

namespace Rubix\Server\Tests;

use Rubix\Server\RESTClient;
use Rubix\Server\Client;
use Rubix\Server\AsyncClient;
use PHPUnit\Framework\TestCase;

/**
 * @group Clients
 * @covers \Rubix\Server\RESTClient
 */
class RESTClientTest extends TestCase
{
    /**
     * @var \Rubix\Server\RESTClient
     */
    protected $client;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->client = new RESTClient('127.0.0.1', 8888, false, [], 0.0, 3);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(RESTClient::class, $this->client);
        $this->assertInstanceOf(Client::class, $this->client);
        $this->assertInstanceOf(AsyncClient::class, $this->client);
    }
}
