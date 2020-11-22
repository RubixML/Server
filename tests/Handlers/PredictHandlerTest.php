<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Queries\Predict;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Payloads\PredictPayload;
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
        $query = new Predict(new Unlabeled(self::SAMPLES));

        $payload = call_user_func($this->handler, $query);

        $this->assertInstanceOf(PredictPayload::class, $payload);
        $this->assertEquals(self::EXPECTED_PREDICTIONS, $payload->predictions());
    }
}
