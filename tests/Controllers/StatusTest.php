<?php

namespace Rubix\Server\Tests\Controllers;

use Rubix\Server\RESTServer;
use Rubix\Server\Controllers\Status;
use Rubix\Server\Controllers\Controller;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $server = $this->createMock(RESTServer::class);

        $server->method('requests')->willReturn(350);
        $server->method('uptime')->willReturn(500);
        
        $this->controller = new Status($server);
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(Status::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle()
    {
        $request = new ServerRequest('GET', '/status');

        $response = $this->controller->handle($request, []);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $status = json_decode($response->getBody()->getContents());

        $this->assertEquals(500, $status->uptime);
        $this->assertEquals(350, $status->requests->count);
        $this->assertEquals(42, $status->requests->requests_min);
        $this->assertEquals(0.7, $status->requests->requests_sec);
    }
}