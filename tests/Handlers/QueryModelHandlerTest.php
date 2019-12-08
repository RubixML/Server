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
    /**
     * @var \Rubix\Server\Handlers\QueryModelHandler
     */
    protected $handler;

    public function setUp() : void
    {
        $this->handler = new QueryModelHandler(new DummyClassifier());
    }

    public function test_build_handler() : void
    {
        $this->assertInstanceOf(QueryModelHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command() : void
    {
        $response = $this->handler->handle(new QueryModel());

        $expected = [
            'type' => 'classifier',
            'compatibility' => [
                'continuous',
                'categorical',
                'resource',
                'other',
            ],
            'probabilistic' => false,
            'ranking' => false
        ];

        $this->assertInstanceOf(QueryModelResponse::class, $response);
        $this->assertEquals($expected, $response->asArray());
    }
}
