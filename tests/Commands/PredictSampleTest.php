<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\PredictSample;
use PHPUnit\Framework\TestCase;

/**
 * @group Commands
 * @covers \Rubix\Server\Commands\PredictSample
 */
class PredictSampleTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    /**
     * @var \Rubix\Server\Commands\PredictSample
     */
    protected $command;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->command = new PredictSample(self::SAMPLE);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(PredictSample::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    /**
     * @test
     */
    public function sample() : void
    {
        $this->assertEquals(self::SAMPLE, $this->command->sample());
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'sample' => ['nice', 'rough', 'loner'],
        ];

        $payload = $this->command->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
