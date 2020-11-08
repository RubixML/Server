<?php

namespace Rubix\Server\Tests\Payloads;

use Rubix\Server\Payloads\Payload;
use Rubix\Server\Payloads\PredictSamplePayload;
use PHPUnit\Framework\TestCase;

/**
 * @group Payloads
 * @covers \Rubix\Server\Payloads\PredictSamplePayload
 */
class PredictSamplePayloadTest extends TestCase
{
    protected const EXPECTED_PREDICTION = 'not monster';

    /**
     * @var \Rubix\Server\Payloads\PredictSamplePayload
     */
    protected $payload;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->payload = new PredictSamplePayload(self::EXPECTED_PREDICTION);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(PredictSamplePayload::class, $this->payload);
        $this->assertInstanceOf(Payload::class, $this->payload);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'prediction' => self::EXPECTED_PREDICTION,
        ];

        $payload = $this->payload->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
