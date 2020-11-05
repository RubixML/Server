<?php

namespace Rubix\Server\Tests\Http\Middleware;

use Rubix\ML\Other\Loggers\BlackHole;
use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Middleware\AccessLogGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\Http\Middleware\AccessLogGenerator
 */
class AccessLogGeneratorTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Middleware\AccessLogGenerator
     */
    protected $middleware;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->middleware = new AccessLogGenerator(new BlackHole());
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(AccessLogGenerator::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }
}
