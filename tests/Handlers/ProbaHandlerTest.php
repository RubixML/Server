<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\ML\Classifiers\GaussianNB;
use PHPUnit\Framework\TestCase;

class ProbaHandlerTest extends TestCase
{
    protected $handler;

    public function setUp()
    {
        $this->handler = new ProbaHandler(new GaussianNB());
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(ProbaHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }
}