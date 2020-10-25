<?php

namespace Rubix\Server\Tests\Http\Middleware;

use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Middleware\ResponseTime;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\Http\Middleware\ResponseTime
 */
class ResponseTimeTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Middleware\ResponseTime
     */
    protected $middleware;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->middleware = new ResponseTime();
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ResponseTime::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }
}
