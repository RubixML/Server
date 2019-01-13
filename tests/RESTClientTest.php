<?php

namespace Rubix\Server\Tests;

use Rubix\Server\Client;
use Rubix\Server\RESTClient;
use PHPUnit\Framework\TestCase;

class RESTClientTest extends TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = new RESTClient('127.0.0.1', 8888, false, []);
    }

    public function test_build_server()
    {
        $this->assertInstanceOf(RESTClient::class, $this->client);
        $this->assertInstanceOf(Client::class, $this->client);
    }
}
