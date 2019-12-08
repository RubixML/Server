<?php

namespace Rubix\Server\Tests\Serializers;

use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\Command;
use Rubix\Server\Serializers\Native;
use Rubix\Server\Serializers\Serializer;
use PHPUnit\Framework\TestCase;

class NativeTest extends TestCase
{
    /**
     * @var \Rubix\Server\Serializers\Native
     */
    protected $serializer;

    public function setUp() : void
    {
        $this->serializer = new Native();
    }

    public function test_build_serialzer() : void
    {
        $this->assertInstanceOf(Native::class, $this->serializer);
        $this->assertInstanceOf(Serializer::class, $this->serializer);
    }

    public function test_serialize_unserialize() : void
    {
        $command = new QueryModel();

        $data = $this->serializer->serialize($command);
        
        $this->assertInternalType('string', $data);
        $this->assertNotEmpty($data);

        $command = $this->serializer->unserialize($data);

        $this->assertInstanceOf(QueryModel::class, $command);
        $this->assertInstanceOf(Command::class, $command);
    }
}
