<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\ProbaSample;
use PHPUnit\Framework\TestCase;

class ProbaSampleTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    protected $command;

    public function setUp()
    {
        $this->command = new ProbaSample(self::SAMPLE);
    }

    public function test_build_command()
    {
        $this->assertInstanceOf(ProbaSample::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_sample()
    {
        $this->assertEquals(self::SAMPLE, $this->command->sample());
    }

    public function test_as_array()
    {
        $expected = [
            'sample' => ['nice', 'rough', 'loner'],
        ];

        $payload = $this->command->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
