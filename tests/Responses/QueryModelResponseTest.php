<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\QueryModelResponse;
use PHPUnit\Framework\TestCase;

class QueryModelResponseTest extends TestCase
{
    protected $response;

    public function setUp()
    {
        $this->response = new QueryModelResponse('Classifier', ['Categorical'], true);
    }

    public function test_build_response()
    {
        $this->assertInstanceOf(QueryModelResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function test_as_array()
    {
        $payload = $this->response->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals('Classifier', $payload['type']);
        $this->assertContains('Categorical', $payload['compatibility']);
        $this->assertTrue($payload['probabilistic']);
    }
}
