<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Handlers\PredictSampleHandler;
use Rubix\Server\Payloads\PredictSamplePayload;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\PredictSampleHandler
 */
class PredictSampleHandlerTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    protected const EXPECTED_PREDICTION = 'not monster';

    /**
     * @var \Rubix\Server\Handlers\PredictSampleHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $estimator = $this->createMock(DummyClassifier::class);

        $estimator->method('predictSample')
            ->willReturn(self::EXPECTED_PREDICTION);

        $this->handler = new PredictSampleHandler($estimator);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(PredictSampleHandler::class, $this->handler);
        $this->assertIsCallable($this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $command = new PredictSample(self::SAMPLE);

        $payload = call_user_func($this->handler, $command);

        $this->assertInstanceOf(PredictSamplePayload::class, $payload);
        $this->assertEquals(self::EXPECTED_PREDICTION, $payload->prediction());
    }
}
