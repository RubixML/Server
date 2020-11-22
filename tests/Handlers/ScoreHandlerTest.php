<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Queries\Score;
use Rubix\Server\Handlers\ScoreHandler;
use Rubix\Server\Payloads\ScorePayload;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\ScoreHandler
 */
class ScoreHandlerTest extends TestCase
{
    protected const SAMPLES = [
        ['nice', 'rough', 'loner'],
        ['mean', 'furry', 'loner'],
        ['nice', 'rough', 'friendly'],
    ];

    protected const EXPECTED_SCORES = [
        6, 4, 10,
    ];

    /**
     * @var \Rubix\Server\Handlers\ScoreHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $estimator = $this->createMock(IsolationForest::class);

        $estimator->method('score')
            ->willReturn(self::EXPECTED_SCORES);

        $this->handler = new ScoreHandler($estimator);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ScoreHandler::class, $this->handler);
        $this->assertIsCallable($this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $query = new Score(new Unlabeled(self::SAMPLES));

        $payload = call_user_func($this->handler, $query);

        $this->assertInstanceOf(ScorePayload::class, $payload);
        $this->assertEquals(self::EXPECTED_SCORES, $payload->scores());
    }
}
