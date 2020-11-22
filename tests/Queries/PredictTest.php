<?php

namespace Rubix\Server\Tests\Queries;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Queries\Query;
use Rubix\Server\Queries\Predict;
use PHPUnit\Framework\TestCase;

/**
 * @group Queries
 * @covers \Rubix\Server\Queries\Predict
 */
class PredictTest extends TestCase
{
    protected const SAMPLES = [
        ['nice', 'rough', 'loner'],
    ];

    /**
     * @var \Rubix\Server\Queries\Predict
     */
    protected $query;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->query = new Predict(new Unlabeled(self::SAMPLES));
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(Predict::class, $this->query);
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
                ['nice', 'rough', 'loner'],
            ],
        ];

        $payload = $this->query->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
