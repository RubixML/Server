<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\RESTServer;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Handlers\ServerStatusHandler;
use Rubix\Server\Responses\ServerStatusResponse;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\ServerStatusHandler
 */
class ServerStatusHandlerTest extends TestCase
{
    /**
     * @var \Rubix\Server\Handlers\ServerStatusHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $server = $this->createMock(RESTServer::class);

        $server->method('requests')
            ->willReturn(5);

        $server->method('uptime')
            ->willReturn(10);

        $this->handler = new ServerStatusHandler($server);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ServerStatusHandler::class, $this->handler);
        $this->assertIsCallable($this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $command = new ServerStatus();

        $response = call_user_func($this->handler, $command);

        $this->assertInstanceOf(ServerStatusResponse::class, $response);
    }
}
