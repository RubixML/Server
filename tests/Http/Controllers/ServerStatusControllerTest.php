<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\ServerStatusController;
use Rubix\Server\Http\Controllers\Controller;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class ServerStatusControllerTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')->willReturn([
            'requests' => [
                'count' => 350,
                'requests_min' => 42,
                'requests_sec' => 0.7,
            ],
            'memory_usage' => [
                'current' => 250,
                'peak' => 500,
            ],
            'uptime' => 7000,
        ]);
        
        $this->controller = new ServerStatusController($commandBus);
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(ServerStatusController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle()
    {
        $request = new ServerRequest('GET', '/status');

        $response = $this->controller->handle($request, []);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $status = json_decode($response->getBody()->getContents());

        $this->assertEquals(7000, $status->uptime);
        $this->assertEquals(350, $status->requests->count);
        $this->assertEquals(42, $status->requests->requests_min);
        $this->assertEquals(0.7, $status->requests->requests_sec);
    }
}