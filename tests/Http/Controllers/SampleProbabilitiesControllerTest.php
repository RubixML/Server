<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\SampleProbabilitiesController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\ProbaSampleResponse;
use React\Http\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\SampleProbabilitiesController
 */
class SampleProbabilitiesControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Controllers\SampleProbabilitiesController
     */
    protected $controller;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new ProbaSampleResponse([]));

        $this->controller = new SampleProbabilitiesController($commandBus);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(SampleProbabilitiesController::class, $this->controller);
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
