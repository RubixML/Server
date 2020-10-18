<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\PredictionsController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\PredictResponse;
use React\Http\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\PredictionsController
 */
class PredictionsControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Controllers\PredictionsController
     */
    protected $controller;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new PredictResponse([]));

        $this->controller = new PredictionsController($commandBus);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(PredictionsController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $request = new ServerRequest('POST', '/example', [], json_encode([
            'samples' => [
                ['The first step is to establish that something is possible, then probability will occur.'],
            ],
        ]) ?: '');

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
