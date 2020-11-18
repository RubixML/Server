<?php

namespace Rubix\Server\Tests\Queries;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Queries\Query;
use Rubix\Server\Queries\Score;
use PHPUnit\Framework\TestCase;

/**
 * @group Queries
 * @covers \Rubix\Server\Queries\Score
 */
class ScoreTest extends TestCase
{
    protected const SAMPLES = [
        ['mean', 'furry', 'friendly'],
    ];

    /**
     * @var \Rubix\Server\Queries\Score
     */
    protected $query;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->query = new Score(new Unlabeled(self::SAMPLES));
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(Score::class, $this->query);
        $this->assertInstanceOf(Query::class, $this->query);
    }

    /**
     * @test
     */
    public function dataset() : void
    {
        $this->assertInstanceOf(Dataset::class, $this->query->dataset());
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

        $payload = $this->query->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
