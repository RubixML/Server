<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use PHPUnit\Framework\TestCase;

class PredictTest extends TestCase
{
    protected const SAMPLES = [
        ['nice', 'rough', 'loner'],
    ];

    /**
     * @var \Rubix\Server\Commands\Predict
     */
    protected $command;

    public function setUp() : void
    {
        $this->command = new Predict(new Unlabeled(self::SAMPLES));
    }

    public function test_build_command() : void
    {
        $this->assertInstanceOf(Predict::class, $this->command);
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
                ['nice', 'rough', 'loner'],
            ],
        ];

        $payload = $this->command->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
