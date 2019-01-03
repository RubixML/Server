<?php

namespace Rubix\Server\Tests\Controllers;

use Rubix\Server\RESTServer;
use Rubix\Server\Controllers\Status;
use Rubix\Server\Controllers\Controller;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $server = $this->createMock(RESTServer::class);
        
        $this->controller = new Status($server);
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(Status::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }
}