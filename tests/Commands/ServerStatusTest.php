<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\ServerStatus;
use PHPUnit\Framework\TestCase;

class ServerStatusTest extends TestCase
{
    /**
     * @var \Rubix\Server\Commands\ServerStatus
     */
    protected $command;

    public function setUp() : void
    {
        $this->command = new ServerStatus();
    }

    public function test_build_command() : void
    {
        $this->assertInstanceOf(ServerStatus::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_as_array() : void
    {
        $expected = [];
        
        $payload = $this->command->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
