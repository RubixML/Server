<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\ServerStatusController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\ServerStatusResponse;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class ServerStatusControllerTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new ServerStatusResponse([], [], 2));
        
        $this->controller = new ServerStatusController($commandBus);
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(ServerStatusController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle_request()
    {
        $request = new ServerRequest('GET', '/status');

        $response = $this->controller->handle($request, []);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
