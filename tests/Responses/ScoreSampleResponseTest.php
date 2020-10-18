<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\ScoreSampleResponse;
use PHPUnit\Framework\TestCase;

class ScoreSampleResponseTest extends TestCase
{
    protected const EXPECTED_SCORE = 4.5;

    /**
     * @var \Rubix\Server\Responses\ScoreSampleResponse
     */
    protected $response;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->response = new ScoreSampleResponse(self::EXPECTED_SCORE);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ScoreSampleResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'score' => self::EXPECTED_SCORE,
        ];

        $payload = $this->response->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
