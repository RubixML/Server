<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\PredictSampleResponse;
use PHPUnit\Framework\TestCase;

class PredictSampleResponseTest extends TestCase
{
    protected const EXPECTED_PREDICTION = 'not monster';

    protected $response;

    public function setUp()
    {
        $this->response = new PredictSampleResponse(self::EXPECTED_PREDICTION);
    }

    public function test_build_response()
    {
        $this->assertInstanceOf(PredictSampleResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function test_as_array()
    {
        $expected = [
            'prediction' => self::EXPECTED_PREDICTION,
        ];
        
        $payload = $this->response->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
