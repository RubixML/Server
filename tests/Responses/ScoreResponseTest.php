<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\ScoreResponse;
use PHPUnit\Framework\TestCase;

class ScoreResponseTest extends TestCase
{
    protected const EXPECTED_SCORES = [
        6, 9, 10,
    ];

    /**
     * @var \Rubix\Server\Responses\ScoreResponse
     */
    protected $response;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->response = new ScoreResponse(self::EXPECTED_SCORES);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ScoreResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'scores' => self::EXPECTED_SCORES,
        ];

        $payload = $this->response->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
