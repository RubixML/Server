<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\ML\EstimatorType;
use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\QueryModelController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Responses\QueryModelResponse;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\QueryModelController
 */
class QueryModelControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Controllers\QueryModelController
     */
    protected $controller;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $commandBus = $this->createMock(CommandBus::class);

        $commandBus->method('dispatch')
            ->willReturn(new QueryModelResponse(EstimatorType::classifier(), [], true, false));

        $this->controller = new QueryModelController($commandBus);
    }

    /**
     * test
     */
    public function build() : void
    {
        $this->assertInstanceOf(QueryModelController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $request = new ServerRequest('GET', '/status');

        $response = $this->controller->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
