<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\RESTServer;
use Rubix\Server\Handlers\ServerStatusHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

class ServerStatusHandlerTest extends TestCase
{
    protected $handler;

    public function setUp()
    {
        $server = $this->createMock(RESTServer::class);

        $this->handler = new ServerStatusHandler($server);
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(ServerStatusHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }
}