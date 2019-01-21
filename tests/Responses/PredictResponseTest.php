<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\PredictResponse;
use PHPUnit\Framework\TestCase;

class PredictResponseTest extends TestCase
{
    protected $response;

    public function setUp()
    {
        $this->response = new PredictResponse([
            'not monster'
        ]);
    }

    public function test_build_response()
    {
        $this->assertInstanceOf(PredictResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function test_as_array()
    {
        $expected = [
            'predictions' => [
                'not monster',
            ],
        ];
        
        $payload = $this->response->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}