<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\QueryModel;
use PHPUnit\Framework\TestCase;

class QueryModelTest extends TestCase
{
    protected $command;

    public function setUp()
    {
        $this->command = new QueryModel();
    }

    public function test_build_command()
    {
        $this->assertInstanceOf(QueryModel::class, $this->command);
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
