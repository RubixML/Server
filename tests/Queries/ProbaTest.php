<?php

namespace Rubix\Server\Tests\Queries;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Queries\Query;
use Rubix\Server\Queries\Proba;
use PHPUnit\Framework\TestCase;

/**
 * @group Queries
 * @covers \Rubix\Server\Queries\Proba
 */
class ProbaTest extends TestCase
{
    protected const SAMPLES = [
        ['mean', 'furry', 'friendly'],
    ];

    /**
     * @var \Rubix\Server\Queries\Proba
     */
    protected $query;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->query = new Proba(new Unlabeled(self::SAMPLES));
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(Proba::class, $this->query);
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
