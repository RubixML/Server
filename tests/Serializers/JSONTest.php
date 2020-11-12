<?php

namespace Rubix\Server\Tests\Serializers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Command;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * @group Serializers
 * @covers \Rubix\Server\Serializers\JSON
 */
class JSONTest extends TestCase
{
    /**
     * @var \Rubix\Server\Serializers\JSON
     */
    protected $serializer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->serializer = new JSON();
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(JSON::class, $this->serializer);
        $this->assertInstanceOf(Serializer::class, $this->serializer);
    }

    /**
     * @test
     */
    public function serializeUnserialize() : void
    {
        $command = new Predict(new Unlabeled(['beef']));

        $data = $this->serializer->serialize($command);

        $this->assertIsString($data);
        $this->assertNotEmpty($data);

        $command = $this->serializer->unserialize($data);

        $this->assertInstanceOf(Predict::class, $command);
        $this->assertInstanceOf(Command::class, $command);
    }
}
