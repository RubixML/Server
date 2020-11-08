<?php

namespace Rubix\Server\Tests\Payloads;

use Rubix\Server\Payloads\Payload;
use Rubix\Server\Payloads\ScoreSamplePayload;
use PHPUnit\Framework\TestCase;

/**
 * @group Payloads
 * @covers \Rubix\Server\Payloads\ScoreSamplePayload
 */
class ScoreSamplePayloadTest extends TestCase
{
    protected const EXPECTED_SCORE = 4.5;

    /**
     * @var \Rubix\Server\Payloads\ScoreSamplePayload
     */
    protected $payload;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->payload = new ScoreSamplePayload(self::EXPECTED_SCORE);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ScoreSamplePayload::class, $this->payload);
        $this->assertInstanceOf(Payload::class, $this->payload);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'score' => self::EXPECTED_SCORE,
        ];

        $payload = $this->payload->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
