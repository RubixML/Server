<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Rank;
use Rubix\Server\Handlers\RankHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\RankResponse;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\RankHandler
 */
class RankHandlerTest extends TestCase
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
     * @var \Rubix\Server\Handlers\RankHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $estimator = $this->createMock(IsolationForest::class);

        $estimator->method('rank')
            ->willReturn(self::EXPECTED_SCORES);

        $this->handler = new RankHandler($estimator);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(RankHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $response = $this->handler->handle(new Rank(new Unlabeled(self::SAMPLES)));

        $this->assertInstanceOf(RankResponse::class, $response);
        $this->assertEquals(self::EXPECTED_SCORES, $response->scores());
    }
}
