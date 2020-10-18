<?php

namespace Rubix\Server\Tests\Commands;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Score;
use PHPUnit\Framework\TestCase;

/**
 * @group Commands
 * @covers \Rubix\Server\Commands\Score
 */
class ScoreTest extends TestCase
{
    protected const SAMPLES = [
        ['mean', 'furry', 'friendly'],
    ];

    /**
     * @var \Rubix\Server\Commands\Score
     */
    protected $command;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->command = new Score(new Unlabeled(self::SAMPLES));
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(Score::class, $this->command);
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
                ['mean', 'furry', 'friendly'],
            ],
        ];

        $payload = $this->command->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
