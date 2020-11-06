<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Services\CommandBus;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Http\Controllers\CommandsController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\PredictResponse;
use React\Http\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\CommandsController
 */
class CommandsControllerTest extends TestCase
{
    protected const SAMPLES = [
        ['The first step is to establish that something is possible, then probability will occur.'],
    ];

    /**
     * @var \Rubix\Server\Http\Controllers\CommandsController
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

        $this->controller = new CommandsController($commandBus, new JSON());
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(CommandsController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $dataset = new Unlabeled(self::SAMPLES);

        $serializer = new JSON();

        $data = $serializer->serialize(new Predict($dataset));

        $request = new ServerRequest('POST', '/', [], $data);

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
