<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\ProbabilitiesController;
use Rubix\Server\Http\Controllers\Controller;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class ProbabilitiesTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')->willReturn([
            'probabilities' => [
                [
                    'positive' => 0.8,
                    'negative' => 0.2,
                ],
            ],
        ]);

        $this->controller = new ProbabilitiesController($commandBus);
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(ProbabilitiesController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle()
    {
        $request = new ServerRequest('POST', '/example', [], json_encode([
            'samples' => [
                ['The first step is to establish that something is possible, then probability will occur.'],
            ],
        ]) ?: null);

        $response = $this->controller->handle($request, ['model' => 'test']);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $probabilities = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals([
            'positive' => 0.8,
            'negative' => 0.2,
        ], $probabilities['probabilities'][0]);
    }
}