<?php

namespace Rubix\Server\Tests\Http\Controllers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Services\QueryBus;
use Rubix\Server\Queries\Predict;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Http\Controllers\QueriesController;
use Rubix\Server\Http\Controllers\RPCController;
use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Payloads\PredictPayload;
use React\Http\Message\ServerRequest;
use React\Promise\PromiseInterface;
use React\Promise\Promise;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\Http\Controllers\QueriesController
 */
class QueriesControllerTest extends TestCase
{
    protected const SAMPLES = [
        ['The first step is to establish that something is possible, then probability will occur.'],
    ];

    /**
     * @var \Rubix\Server\Http\Controllers\QueriesController
     */
    protected $controller;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $queryBus = $this->createMock(QueryBus::class);

        $queryBus->method('dispatch')
            ->willReturn(new Promise(function ($resolve) {
                $resolve(new PredictPayload(['positive']));
            }));

        $this->controller = new QueriesController($queryBus, new JSON());
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(QueriesController::class, $this->controller);
        $this->assertInstanceOf(RPCController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $dataset = new Unlabeled(self::SAMPLES);

        $query = new Predict($dataset);

        $serializer = new JSON();

        $data = $serializer->serialize($query);

        $request = new ServerRequest('POST', '/', [], $data);

        $request = $request->withParsedBody($query);

        $promise = call_user_func($this->controller, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);
    }
}
