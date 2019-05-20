<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Serializers\Json;
use Rubix\Server\Http\Controllers\RPCController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\PredictResponse;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class RPCControllerTest extends TestCase
{
    protected $controller;

    protected $serializer;

    public function setUp()
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new PredictResponse([]));

        $this->serializer = new Json();

        $this->controller = new RPCController($commandBus, $this->serializer);
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(RPCController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle_request()
    {
        $data = $this->serializer->serialize(new Predict([
            ['The first step is to establish that something is possible, then probability will occur.'],
        ]));

        $request = new ServerRequest('POST', '/', [], $data);

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
