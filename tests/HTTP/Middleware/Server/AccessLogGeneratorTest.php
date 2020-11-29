<?php

namespace Rubix\Server\Tests\HTTP\Middleware\Server;

use Rubix\ML\Other\Loggers\BlackHole;
use Rubix\Server\HTTP\Middleware\Server\Middleware;
use Rubix\Server\HTTP\Middleware\Server\AccessLogGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\HTTP\Middleware\Server\AccessLogGenerator
 */
class AccessLogGeneratorTest extends TestCase
{
    /**
     * @var \Rubix\Server\HTTP\Middleware\Server\AccessLogGenerator
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
