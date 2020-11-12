<?php

namespace Rubix\Server\Tests\Serializers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Command;
use Rubix\Server\Serializers\Native;
use Rubix\Server\Serializers\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * @group Serializers
 * @covers \Rubix\Server\Serializers\Native
 */
class NativeTest extends TestCase
{
    /**
     * @var \Rubix\Server\Serializers\Native
     */
    protected $serializer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->serializer = new Native();
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(Native::class, $this->serializer);
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
