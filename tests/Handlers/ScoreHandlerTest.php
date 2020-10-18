<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Score;
use Rubix\Server\Handlers\ScoreHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\ScoreResponse;
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
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $response = $this->handler->handle(new Score(new Unlabeled(self::SAMPLES)));

        $this->assertInstanceOf(ScoreResponse::class, $response);
        $this->assertEquals(self::EXPECTED_SCORES, $response->scores());
    }
}
