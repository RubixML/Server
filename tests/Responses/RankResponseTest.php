<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\RankResponse;
use PHPUnit\Framework\TestCase;

class RankResponseTest extends TestCase
{
    protected const EXPECTED_SCORES = [
        6, 9, 10,
    ];

    protected $response;

    public function setUp()
    {
        $this->response = new RankResponse(self::EXPECTED_SCORES);
    }

    public function test_build_response()
    {
        $this->assertInstanceOf(RankResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function test_as_array()
    {
        $expected = [
            'scores' => self::EXPECTED_SCORES,
        ];
        
        $payload = $this->response->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
