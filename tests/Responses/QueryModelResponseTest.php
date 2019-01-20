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
        $this->response = new QueryModelResponse('Classifier', true);
    }

    public function test_build_response()
    {
        $this->assertInstanceOf(QueryModelResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }
}