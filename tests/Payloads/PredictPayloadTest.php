<?php

namespace Rubix\Server\Tests\Payloads;

use Rubix\Server\Payloads\Payload;
use Rubix\Server\Payloads\PredictPayload;
use PHPUnit\Framework\TestCase;

/**
 * @group Payloads
 * @covers \Rubix\Server\Payloads\PredictPayload
 */
class PredictPayloadTest extends TestCase
{
    protected const EXPECTED_PREDICTIONS = [
        'not monster',
        'monster',
        'not monster',
    ];

    /**
     * @var \Rubix\Server\Payloads\PredictPayload
     */
    protected $payload;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->payload = new PredictPayload(self::EXPECTED_PREDICTIONS);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(PredictPayload::class, $this->payload);
        $this->assertInstanceOf(Payload::class, $this->payload);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'predictions' => self::EXPECTED_PREDICTIONS,
        ];

        $payload = $this->payload->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
