<?php

namespace Rubix\Server\Tests\Payloads;

use Rubix\Server\Payloads\Payload;
use Rubix\Server\Payloads\ScorePayload;
use PHPUnit\Framework\TestCase;

/**
 * @group Payloads
 * @covers \Rubix\Server\Payloads\ScorePayload
 */
class ScorePayloadTest extends TestCase
{
    protected const EXPECTED_SCORES = [
        6, 9, 10,
    ];

    /**
     * @var \Rubix\Server\Payloads\ScorePayload
     */
    protected $payload;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->payload = new ScorePayload(self::EXPECTED_SCORES);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ScorePayload::class, $this->payload);
        $this->assertInstanceOf(Payload::class, $this->payload);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'data' => self::EXPECTED_SCORES,
        ];

        $payload = $this->payload->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
