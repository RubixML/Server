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

    /**
     * @var \Rubix\Server\Commands\Proba
     */
    protected $command;

    public function setUp() : void
    {
        $this->command = new Proba(new Unlabeled(self::SAMPLES));
    }

    public function test_build_command() : void
    {
        $this->assertInstanceOf(Proba::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_dataset() : void
    {
        $this->assertInstanceOf(Dataset::class, $this->command->dataset());
    }

    public function test_as_array() : void
    {
        $expected = [
            'samples' => [
                ['mean', 'furry', 'friendly'],
            ],
        ];
        
        $payload = $this->command->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
