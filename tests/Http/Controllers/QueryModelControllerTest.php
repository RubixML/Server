<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\QueryModelController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\QueryModelResponse;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class QueryModelControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Controllers\QueryModelController
     */
    protected $controller;

    public function setUp() : void
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new QueryModelResponse('Classifier', [], true, false));
        
        $this->controller = new QueryModelController($commandBus);
    }

    public function test_build_controller() : void
    {
        $this->assertInstanceOf(QueryModelController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle_request() : void
    {
        $request = new ServerRequest('GET', '/status');

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
