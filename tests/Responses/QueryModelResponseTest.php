<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\ML\DataType;
use Rubix\ML\EstimatorType;
use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\QueryModelResponse;
use PHPUnit\Framework\TestCase;

class QueryModelResponseTest extends TestCase
{
    /**
     * @var \Rubix\Server\Responses\QueryModelResponse
     */
    protected $response;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->response = new QueryModelResponse(
            EstimatorType::classifier(),
            [DataType::categorical()],
            true,
            false
        );
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(QueryModelResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $payload = $this->response->asArray();

        $this->assertIsArray($payload);
        
        $this->assertEquals(EstimatorType::classifier(), $payload['type']);
        $this->assertEquals([DataType::categorical()], $payload['compatibility']);
        $this->assertTrue($payload['probabilistic']);
        $this->assertFalse($payload['ranking']);
    }
}
