<?php

namespace Rubix\Server\Tests\Http\Middleware;

use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\Http\Middleware\SharedTokenAuthenticator
 */
class SharedTokenAuthenticatorTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Middleware\SharedTokenAuthenticator
     */
    protected $middleware;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->middleware = new SharedTokenAuthenticator('secret');
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(SharedTokenAuthenticator::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }
}
