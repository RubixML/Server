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
            [1.0, 3.2, -1.5],
            [0.5, -6.0, 2.9],
        ]);
    }

    public function test_build_command()
    {
        $this->assertInstanceOf(Predict::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }
}