<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use PHPUnit\Framework\TestCase;

class PredictTest extends TestCase
{
    protected $command;

    public function setUp()
    {
        $this->command = new Predict([
            ['nice', 'rough', 'loner'],
        ]);
    }

    public function test_build_command()
    {
        $this->assertInstanceOf(Predict::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_as_array()
    {
        $expected = [
            'samples' => [
                ['nice', 'rough', 'loner'],
            ],
        ];

        $payload = $this->command->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
