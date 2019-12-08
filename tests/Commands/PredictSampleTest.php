<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\PredictSample;
use PHPUnit\Framework\TestCase;

class PredictSampleTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    /**
     * @var \Rubix\Server\Commands\PredictSample
     */
    protected $command;

    public function setUp() : void
    {
        $this->command = new PredictSample(self::SAMPLE);
    }

    public function test_build_command() : void
    {
        $this->assertInstanceOf(PredictSample::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_sample() : void
    {
        $this->assertEquals(self::SAMPLE, $this->command->sample());
    }

    public function test_as_array() : void
    {
        $expected = [
            'sample' => ['nice', 'rough', 'loner'],
        ];

        $payload = $this->command->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
