<?php

namespace Rubix\Server\Tests\Http\Middleware;

use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use PHPUnit\Framework\TestCase;

class SharedTokenAuthenticatorTest extends TestCase
{
    protected $middleware;

    public function setUp()
    {
        $this->middleware = new SharedTokenAuthenticator('secret');
    }

    public function test_build_middleware()
    {
        $this->assertInstanceOf(SharedTokenAuthenticator::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }
}
