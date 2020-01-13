<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\CommandBus;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Serializers\Json;
use Rubix\Server\Http\Controllers\RPCController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\PredictResponse;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\RPCController
 */
class RPCControllerTest extends TestCase
{
    protected const SAMPLES = [
        ['The first step is to establish that something is possible, then probability will occur.'],
    ];
    
    /**
     * @var \Rubix\Server\Http\Controllers\RPCController
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

        $this->controller = new RPCController($commandBus, new Json());
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(RPCController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $dataset = new Unlabeled(self::SAMPLES);

        $serializer = new Json();

        $data = $serializer->serialize(new Predict($dataset));

        $request = new ServerRequest('POST', '/', [], $data);

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
