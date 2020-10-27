<?php

namespace Rubix\Server\Tests\Http\Middleware;

use Rubix\ML\Other\Loggers\BlackHole;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Middleware\AccessLog;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\Http\Middleware\AccessLog
 */
class AccessLogTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Middleware\AccessLog
     */
    protected $middleware;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->middleware = new AccessLog(new BlackHole());
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(AccessLog::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }
}
