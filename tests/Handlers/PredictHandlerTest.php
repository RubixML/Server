<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\PredictResponse;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

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
    
    protected $handler;

    public function setUp()
    {
        $estimator = $this->createMock(DummyClassifier::class);

        $estimator->method('predict')
            ->willReturn(self::EXPECTED_PREDICTIONS);

        $this->handler = new PredictHandler($estimator);
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(PredictHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command()
    {
        $response = $this->handler->handle(new Predict(new Unlabeled(self::SAMPLES)));

        $this->assertInstanceOf(PredictResponse::class, $response);
        $this->assertEquals(self::EXPECTED_PREDICTIONS, $response->predictions());
    }
}
