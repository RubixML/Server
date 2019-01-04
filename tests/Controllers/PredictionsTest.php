<?php

namespace Rubix\Server\Tests\Controllers;

use Rubix\Server\Controllers\Predictions;
use Rubix\Server\Controllers\Controller;
use Rubix\ML\Classifiers\DummyClassifier;
use React\Http\Io\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use PHPUnit\Framework\TestCase;

class PredictionsTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $estimator = $this->createMock(DummyClassifier::class);

        $estimator->method('predict')->willReturn(['positive']);

        $this->controller = new Predictions($estimator);
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(Predictions::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    public function test_handle()
    {
        $request = new ServerRequest('POST', '/example', [], json_encode([
            'samples' => [
                ['The first step is to establish that something is possible, then probability will occur.'],
            ],
        ]) ?: null);

        $response = $this->controller->handle($request, []);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $prediction = json_decode($response->getBody()->getContents());

        $this->assertEquals('positive', $prediction[0]);
    }
}