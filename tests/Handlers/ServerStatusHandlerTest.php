<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\RESTServer;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Handlers\ServerStatusHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\ServerStatusResponse;
use PHPUnit\Framework\TestCase;

class ServerStatusHandlerTest extends TestCase
{
    protected $command;

    protected $handler;

    public function setUp()
    {
        $this->command = new ServerStatus();

        $server = $this->createMock(RESTServer::class);

        $server->method('requests')
            ->willReturn(5);

        $server->method('uptime')
            ->willReturn(10);

        $this->handler = new ServerStatusHandler($server);
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(ServerStatusHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command()
    {
        $response = $this->handler->handle($this->command);

        $this->assertInstanceOf(ServerStatusResponse::class, $response);
    }
}
