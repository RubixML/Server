<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Handlers\QueryModelHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

class QueryModelHandlerTest extends TestCase
{
    protected $handler;

    public function setUp()
    {
        $this->handler = new QueryModelHandler(new DummyClassifier());
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(QueryModelHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }
}