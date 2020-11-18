<?php

namespace Rubix\Server\Tests\Serializers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Queries\Predict;
use Rubix\Server\Queries\Query;
use Rubix\Server\Serializers\Bzip2;
use Rubix\Server\Serializers\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * @group Serializers
 * @requires extension bz2
 * @covers \Rubix\Server\Serializers\Bzip2
 */
class Bzip2Test extends TestCase
{
    /**
     * @var \Rubix\Server\Serializers\Bzip2
     */
    protected $serializer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->serializer = new Bzip2(4, 0);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(Bzip2::class, $this->serializer);
        $this->assertInstanceOf(Serializer::class, $this->serializer);
    }

    /**
     * @test
     */
    public function serializeUnserialize() : void
    {
        $query = new Predict(new Unlabeled(['beef']));

        $data = $this->serializer->serialize($query);

        $this->assertIsString($data);
        $this->assertNotEmpty($data);

        $query = $this->serializer->unserialize($data);

        $this->assertInstanceOf(Predict::class, $query);
        $this->assertInstanceOf(Query::class, $query);
    }
}
