<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\Services\CommandBus;
use Rubix\Server\Http\Controllers\ScoresController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Payloads\ScorePayload;
use React\Http\Message\ServerRequest;
use React\Promise\PromiseInterface;
use React\Promise\Promise;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\ScoresController
 */
class ScoresControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Controllers\ScoresController
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
                $resolve(new ScorePayload([0.9]));
            }));

        $this->controller = new ScoresController($commandBus);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ScoresController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $payload = [
            'samples' => [
                ['The first step is to establish that something is possible, then probability will occur.'],
            ],
        ];

        $request = new ServerRequest('POST', '/example', [], json_encode($payload) ?: '');

        $request = $request->withParsedBody($payload);

        $promise = call_user_func($this->controller, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);
    }
}
