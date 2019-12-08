<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\PredictSampleResponse;
use PHPUnit\Framework\TestCase;

class PredictSampleResponseTest extends TestCase
{
    protected const EXPECTED_PREDICTION = 'not monster';

    /**
     * @var \Rubix\Server\Responses\PredictSampleResponse
     */
    protected $response;

    public function setUp() : void
    {
        $this->response = new PredictSampleResponse(self::EXPECTED_PREDICTION);
    }

    public function test_build_response() : void
    {
        $this->assertInstanceOf(PredictSampleResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function test_as_array() : void
    {
        $expected = [
            'prediction' => self::EXPECTED_PREDICTION,
        ];
        
        $payload = $this->response->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
