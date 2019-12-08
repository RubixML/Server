<?php

namespace Rubix\Server\Tests\Http\Middleware;

use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use PHPUnit\Framework\TestCase;

class SharedTokenAuthenticatorTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Middleware\SharedTokenAuthenticator
     */
    protected $middleware;

    public function setUp() : void
    {
        $this->middleware = new SharedTokenAuthenticator('secret');
    }

    public function test_build_middleware() : void
    {
        $this->assertInstanceOf(SharedTokenAuthenticator::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }
}
