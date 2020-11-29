<?php

namespace Rubix\Server\Tests\HTTP\Middleware\Server;

use Rubix\Server\HTTP\Middleware\Server\Middleware;
use Rubix\Server\HTTP\Middleware\Server\BasicAuthenticator;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\HTTP\Middleware\Server\BasicAuthenticator
 */
class BasicAuthenticatorTest extends TestCase
{
    /**
     * @var \Rubix\Server\HTTP\Middleware\Server\BasicAuthenticator
     */
    protected $middleware;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->middleware = new BasicAuthenticator([
            'user' => 'secret',
        ]);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(BasicAuthenticator::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }
}
