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

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->response = new PredictSampleResponse(self::EXPECTED_PREDICTION);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(PredictSampleResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'prediction' => self::EXPECTED_PREDICTION,
        ];
        
        $payload = $this->response->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
