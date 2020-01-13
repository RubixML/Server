<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\ProbaSampleResponse;
use PHPUnit\Framework\TestCase;

class ProbaSampleResponseTest extends TestCase
{
    protected const EXPECTED_PROBABILITIES = [
        'monster' => 0.4,
        'not monster' => 0.6,
    ];

    /**
     * @var \Rubix\Server\Responses\ProbaSampleResponse
     */
    protected $response;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->response = new ProbaSampleResponse(self::EXPECTED_PROBABILITIES);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ProbaSampleResponse::class, $this->response);
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
