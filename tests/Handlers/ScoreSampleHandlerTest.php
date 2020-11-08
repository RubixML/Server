<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\ScoreSample;
use Rubix\Server\Handlers\ScoreSampleHandler;
use Rubix\Server\Payloads\ScoreSamplePayload;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\ScoreSamplesHandler
 */
class ScoreSampleHandlerTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    protected const EXPECTED_PREDICTION = 4.5;

    /**
     * @var \Rubix\Server\Handlers\ScoreSampleHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $estimator = $this->createMock(IsolationForest::class);

        $estimator->method('scoreSample')
            ->willReturn(self::EXPECTED_PREDICTION);

        $this->handler = new ScoreSampleHandler($estimator);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ScoreSampleHandler::class, $this->handler);
        $this->assertIsCallable($this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $command = new ScoreSample(self::SAMPLE);

        $payload = call_user_func($this->handler, $command);

        $this->assertInstanceOf(ScoreSamplePayload::class, $payload);
        $this->assertEquals(self::EXPECTED_PREDICTION, $payload->score());
    }
}
