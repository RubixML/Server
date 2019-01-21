<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\Predict;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\PredictResponse;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

class PredictHandlerTest extends TestCase
{
    protected $command;
    
    protected $handler;

    public function setUp()
    {
        $estimator = $this->createMock(DummyClassifier::class);

        $estimator->method('predict')
            ->willReturn(['monster']);

        $this->command = new Predict([
            ['mean', 'rough', 'loner'],
        ]);

        $this->handler = new PredictHandler($estimator);
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(PredictHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command()
    {
        $response = $this->handler->handle($this->command);

        $this->assertInstanceOf(PredictResponse::class, $response);
    }
}