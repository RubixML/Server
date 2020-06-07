<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\QueryModel;
use PHPUnit\Framework\TestCase;

/**
 * @group Commands
 * @covers \Rubix\Server\Commands\QueryModel
 */
class QueryModelTest extends TestCase
{
    /**
     * @var \Rubix\Server\Commands\QueryModel
     */
    protected $command;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->command = new QueryModel();
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(QueryModel::class, $this->command);
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
