<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\ProbaResponse;
use PHPUnit\Framework\TestCase;

class ProbaResponseTest extends TestCase
{
    protected const EXPECTED_PROBABILITIES = [
        [
            'monster' => 0.4,
            'not monster' => 0.6,
        ],
    ];

    /**
     * @var \Rubix\Server\Responses\ProbaResponse
     */
    protected $response;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->response = new ProbaResponse(self::EXPECTED_PROBABILITIES);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ProbaResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'probabilities' => self::EXPECTED_PROBABILITIES,
        ];

        $payload = $this->response->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
