<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\ScoreSample;
use PHPUnit\Framework\TestCase;

/**
 * @group Commands
 * @covers \Rubix\Server\Commands\ScoreSample
 */
class ScoreSampleTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    /**
     * @var \Rubix\Server\Commands\ScoreSample
     */
    protected $command;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->command = new ScoreSample(self::SAMPLE);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ScoreSample::class, $this->command);
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
