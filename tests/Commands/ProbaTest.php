<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Proba;
use PHPUnit\Framework\TestCase;

class ProbaTest extends TestCase
{
    protected const SAMPLES = [
        ['mean', 'furry', 'friendly'],
    ];

    protected $command;

    public function setUp()
    {
        $this->command = new Proba(new Unlabeled(self::SAMPLES));
    }

    public function test_build_command()
    {
        $this->assertInstanceOf(Proba::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_dataset()
    {
        $this->assertInstanceOf(Dataset::class, $this->command->dataset());
    }

    public function test_as_array()
    {
        $expected = [
            'samples' => [
                ['mean', 'furry', 'friendly'],
            ],
        ];
        
        $payload = $this->command->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
