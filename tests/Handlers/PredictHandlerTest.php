<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

class PredictHandlerTest extends TestCase
{
    protected $handler;

    public function setUp()
    {
        $this->handler = new PredictHandler(new DummyClassifier());
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(PredictHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }
}