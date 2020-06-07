<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\ServerStatus;
use PHPUnit\Framework\TestCase;

/**
 * @group Commands
 * @covers \Rubix\Server\Commands\ServerStatus
 */
class ServerStatusTest extends TestCase
{
    /**
     * @var \Rubix\Server\Commands\ServerStatus
     */
    protected $command;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->command = new ServerStatus();
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ServerStatus::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [];

        $payload = $this->command->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
