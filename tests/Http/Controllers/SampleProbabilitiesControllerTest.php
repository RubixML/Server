<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\SampleProbabilitiesController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\ProbaSampleResponse;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class SampleProbabilitiesControllerTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new ProbaSampleResponse([]));

        $this->controller = new SampleProbabilitiesController($commandBus);
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(SampleProbabilitiesController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle_request()
    {
        $request = new ServerRequest('POST', '/example', [], json_encode([
            'sample' => ['The first step is to establish that something is possible, then probability will occur.'],
        ]) ?: null);

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
