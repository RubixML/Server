<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\RankSample;
use Rubix\Server\Handlers\RankSampleHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\RankSampleResponse;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\RankSamplesHandler
 */
class RankSampleHandlerTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    protected const EXPECTED_PREDICTION = 4.5;
    
    /**
     * @var \Rubix\Server\Handlers\RankSampleHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $estimator = $this->createMock(IsolationForest::class);

        $estimator->method('rankSample')
            ->willReturn(self::EXPECTED_PREDICTION);

        $this->handler = new RankSampleHandler($estimator);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(RankSampleHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $response = $this->handler->handle(new RankSample(self::SAMPLE));

        $this->assertInstanceOf(RankSampleResponse::class, $response);
        $this->assertEquals(self::EXPECTED_PREDICTION, $response->score());
    }
}
