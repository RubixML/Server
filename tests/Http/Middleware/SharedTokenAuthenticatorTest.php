<?php

namespace Rubix\Server\Tests\HTTP\Middleware;

use Rubix\Server\HTTP\Middleware\Middleware;
use Rubix\Server\HTTP\Middleware\SharedTokenAuthenticator;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\HTTP\Middleware\SharedTokenAuthenticator
 */
class SharedTokenAuthenticatorTest extends TestCase
{
    /**
     * @var \Rubix\Server\HTTP\Middleware\SharedTokenAuthenticator
     */
    protected $middleware;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->middleware = new SharedTokenAuthenticator([
            'secret', 'another-key',
        ]);
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
