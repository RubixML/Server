<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Services\CommandBus;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Http\Controllers\CommandsController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Payloads\PredictPayload;
use React\Http\Message\ServerRequest;
use React\Promise\PromiseInterface;
use React\Promise\Promise;
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
            ->willReturn(new Promise(function ($resolve) {
                $resolve(new PredictPayload(['positive']));
            }));

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

        $command = new Predict($dataset);

        $serializer = new JSON();

        $data = $serializer->serialize($command);

        $request = new ServerRequest('POST', '/', [], $data);

        $request = $request->withParsedBody($command);

        $promise = call_user_func($this->controller, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);
    }
}
