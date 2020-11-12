<?php

namespace Rubix\Server\Tests\Payloads;

use Rubix\Server\Payloads\Payload;
use Rubix\Server\Payloads\ProbaPayload;
use PHPUnit\Framework\TestCase;

/**
 * @group Payloads
 * @covers \Rubix\Server\Payloads\ProbaPayload
 */
class ProbaPayloadTest extends TestCase
{
    protected const EXPECTED_PROBABILITIES = [
        [
            'monster' => 0.4,
            'not monster' => 0.6,
        ],
    ];

    /**
     * @var \Rubix\Server\Payloads\ProbaPayload
     */
    protected $payload;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->payload = new ProbaPayload(self::EXPECTED_PROBABILITIES);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ProbaPayload::class, $this->payload);
        $this->assertInstanceOf(Payload::class, $this->payload);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'probabilities' => self::EXPECTED_PROBABILITIES,
        ];

        $payload = $this->payload->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
