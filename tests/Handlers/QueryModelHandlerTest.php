<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Handlers\QueryModelHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\QueryModelResponse;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\QueryModelHandler
 */
class QueryModelHandlerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Handlers\QueryModelHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->handler = new QueryModelHandler(new DummyClassifier());
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(QueryModelHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
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
