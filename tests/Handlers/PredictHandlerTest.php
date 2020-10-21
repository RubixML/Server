<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Responses\PredictResponse;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\PredictHandler
 */
class PredictHandlerTest extends TestCase
{
    protected const SAMPLES = [
        ['nice', 'rough', 'loner'],
        ['nice', 'rough', 'friendly'],
        ['mean', 'furry', 'friendly'],
    ];

    protected const EXPECTED_PREDICTIONS = [
        'not monster',
        'not monster',
        'monster',
    ];

    /**
     * @var \Rubix\Server\Handlers\PredictHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $estimator = $this->createMock(DummyClassifier::class);

        $estimator->method('predict')
            ->willReturn(self::EXPECTED_PREDICTIONS);

        $this->handler = new PredictHandler($estimator);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(PredictHandler::class, $this->handler);
        $this->assertIsCallable($this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $command = new Predict(new Unlabeled(self::SAMPLES));

        $response = call_user_func($this->handler, $command);

        $this->assertInstanceOf(PredictResponse::class, $response);
        $this->assertEquals(self::EXPECTED_PREDICTIONS, $response->predictions());
    }
}
