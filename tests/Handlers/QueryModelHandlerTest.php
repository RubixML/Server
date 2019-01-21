<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Handlers\QueryModelHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\QueryModelResponse;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

class QueryModelHandlerTest extends TestCase
{
    protected $command;

    protected $handler;

    public function setUp()
    {
        $this->command = new QueryModel();
        
        $this->handler = new QueryModelHandler(new DummyClassifier());
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(QueryModelHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command()
    {
        $response = $this->handler->handle($this->command);

        $this->assertInstanceOf(QueryModelResponse::class, $response);
    }
}