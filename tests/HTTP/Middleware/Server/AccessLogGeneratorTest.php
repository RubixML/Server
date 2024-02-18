<?php

namespace Rubix\Server\Tests\HTTP\Middleware;

use Rubix\ML\Loggers\BlackHole;
use Rubix\Server\HTTP\Middleware\Middleware;
use Rubix\Server\HTTP\Middleware\AccessLogGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\HTTP\Middleware\AccessLogGenerator
 */
class AccessLogGeneratorTest extends TestCase
{
    /**
     * @var AccessLogGenerator
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
