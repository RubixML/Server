<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use PHPUnit\Framework\TestCase;

/**
 * @group Commands
 * @covers \Rubix\Server\Commands\Predict
 */
class PredictTest extends TestCase
{
    protected const SAMPLES = [
        ['nice', 'rough', 'loner'],
    ];

    /**
     * @var \Rubix\Server\Commands\Predict
     */
    protected $command;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->command = new Predict(new Unlabeled(self::SAMPLES));
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(Predict::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    /**
     * @test
     */
    public function dataset() : void
    {
        $this->assertInstanceOf(Dataset::class, $this->command->dataset());
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'samples' => [
                ['nice', 'rough', 'loner'],
            ],
        ];

        $payload = $this->command->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
