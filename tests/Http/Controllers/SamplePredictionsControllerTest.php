<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\Services\CommandBus;
use Rubix\Server\Http\Controllers\SamplePredictionsController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Payloads\PredictSamplePayload;
use React\Http\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\SamplePredictionsController
 */
class SamplePredictionsControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Controllers\SamplePredictionsController
     */
    protected $controller;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new PredictSamplePayload([]));

        $this->controller = new SamplePredictionsController($commandBus);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(SamplePredictionsController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $request = new ServerRequest('POST', '/example', [], json_encode([
            'sample' => ['The first step is to establish that something is possible, then probability will occur.'],
        ]) ?: '');

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
