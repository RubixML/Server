<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\RankSample;
use Rubix\Server\Handlers\RankSampleHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\RankSampleResponse;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use PHPUnit\Framework\TestCase;

class RankSampleHandlerTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    protected const EXPECTED_PREDICTION = 4.5;
    
    protected $handler;

    public function setUp()
    {
        $estimator = $this->createMock(IsolationForest::class);

        $estimator->method('rankSample')
            ->willReturn(self::EXPECTED_PREDICTION);

        $this->handler = new RankSampleHandler($estimator);
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(RankSampleHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command()
    {
        $response = $this->handler->handle(new RankSample(self::SAMPLE));

        $this->assertInstanceOf(RankSampleResponse::class, $response);
        $this->assertEquals(self::EXPECTED_PREDICTION, $response->score());
    }
}
