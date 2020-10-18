<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\ServerStatusController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\ServerStatusResponse;
use React\Http\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\ServerStatusController
 */
class ServerStatusControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Controllers\ServerStatusController
     */
    protected $controller;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new ServerStatusResponse([], [], 2));

        $this->controller = new ServerStatusController($commandBus);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ServerStatusController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $request = new ServerRequest('GET', '/status');

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
