<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\ServerStatus;
use PHPUnit\Framework\TestCase;

class ServerStatusTest extends TestCase
{
    protected $command;

    public function setUp()
    {
        $this->command = new ServerStatus();
    }

    public function test_build_command()
    {
        $this->assertInstanceOf(ServerStatus::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_as_array()
    {
        $expected = [];
        
        $payload = $this->command->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}