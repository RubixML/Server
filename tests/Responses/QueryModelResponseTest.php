<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\QueryModelResponse;
use PHPUnit\Framework\TestCase;

class QueryModelResponseTest extends TestCase
{
    protected const TYPE = 'Classifier';
    protected const COMPATIBILITY = ['Categorical'];
    protected const PROBABILISTIC = true;
    protected const RANKING = false;
    
    /**
     * @var \Rubix\Server\Responses\QueryModelResponse
     */
    protected $response;

    public function setUp() : void
    {
        $this->response = new QueryModelResponse(self::TYPE, self::COMPATIBILITY, self::PROBABILISTIC, self::RANKING);
    }

    public function test_build_response() : void
    {
        $this->assertInstanceOf(QueryModelResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function test_as_array() : void
    {
        $payload = $this->response->asArray();

        $this->assertIsArray($payload);
        
        $this->assertEquals(self::TYPE, $payload['type']);
        $this->assertEquals(self::COMPATIBILITY, $payload['compatibility']);
        $this->assertEquals(self::PROBABILISTIC, $payload['probabilistic']);
        $this->assertEquals(self::RANKING, $payload['ranking']);
    }
}
