<?php

namespace Rubix\Server\Tests\Http\Middleware;

use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Middleware\BasicAuthenticator;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\Http\Middleware\BasicAuthenticator
 */
class BasicAuthenticatorTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Middleware\BasicAuthenticator
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
