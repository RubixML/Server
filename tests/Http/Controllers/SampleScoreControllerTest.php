<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\SampleScoreController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\RankSampleResponse;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class SampleScoreControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Controllers\SampleScoreController
     */
    protected $controller;

    public function setUp() : void
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new RankSampleResponse(6.8));

        $this->controller = new SampleScoreController($commandBus);
    }

    public function test_build_controller() : void
    {
        $this->assertInstanceOf(SampleScoreController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle_request() : void
    {
        $request = new ServerRequest('POST', '/example', [], json_encode([
            'sample' => ['The first step is to establish that something is possible, then probability will occur.'],
        ]) ?: null);

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
