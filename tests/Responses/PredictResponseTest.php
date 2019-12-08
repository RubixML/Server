<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\PredictResponse;
use PHPUnit\Framework\TestCase;

class PredictResponseTest extends TestCase
{
    protected const EXPECTED_PREDICTIONS = [
        'not monster',
        'monster',
        'not monster',
    ];

    /**
     * @var \Rubix\Server\Responses\PredictResponse
     */
    protected $response;

    public function setUp() : void
    {
        $this->response = new PredictResponse(self::EXPECTED_PREDICTIONS);
    }

    public function test_build_response() : void
    {
        $this->assertInstanceOf(PredictResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function test_as_array() : void
    {
        $expected = [
            'predictions' => self::EXPECTED_PREDICTIONS,
        ];
        
        $payload = $this->response->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
