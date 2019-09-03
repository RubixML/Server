<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Handlers\PredictSampleHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\PredictSampleResponse;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

class PredictSampleHandlerTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    protected const EXPECTED_PREDICTION = 'not monster';
    
    protected $handler;

    public function setUp()
    {
        $estimator = $this->createMock(DummyClassifier::class);

        $estimator->method('predictSample')
            ->willReturn(self::EXPECTED_PREDICTION);

        $this->handler = new PredictSampleHandler($estimator);
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(PredictSampleHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command()
    {
        $response = $this->handler->handle(new PredictSample(self::SAMPLE));

        $this->assertInstanceOf(PredictSampleResponse::class, $response);
        $this->assertEquals(self::EXPECTED_PREDICTION, $response->prediction());
    }
}
