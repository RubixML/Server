<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\PredictResponse;
use PHPUnit\Framework\TestCase;

/**
 * @group Responses
 * @covers \Rubix\Server\Responses\PredictResponse
 */
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

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->response = new PredictResponse(self::EXPECTED_PREDICTIONS);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(PredictResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'predictions' => self::EXPECTED_PREDICTIONS,
        ];

        $payload = $this->response->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
