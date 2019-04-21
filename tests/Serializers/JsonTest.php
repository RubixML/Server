<?php

namespace Rubix\Server\Tests\Serializers;

use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\Command;
use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Serializer;
use PHPUnit\Framework\TestCase;

class JSONTest extends TestCase
{
    protected $command;
    
    protected $serializer;

    public function setUp()
    {
        $this->command = new QueryModel();

        $this->serializer = new JSON();
    }

    public function test_build_serialzer()
    {
        $this->assertInstanceOf(JSON::class, $this->serializer);
        $this->assertInstanceOf(Serializer::class, $this->serializer);
    }

    public function test_serialize_unserialize()
    {
        $data = $this->serializer->serialize($this->command);
        
        $this->assertInternalType('string', $data);

        $command = $this->serializer->unserialize($data);

        $this->assertInstanceOf(QueryModel::class, $command);
        $this->assertInstanceOf(Command::class, $command);
    }
}
