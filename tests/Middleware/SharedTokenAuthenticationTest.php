<?php

namespace Rubix\Server\Tests\Middleware;

use Rubix\Server\Middleware\Middleware;
use Rubix\Server\Middleware\SharedTokenAuthentication;
use PHPUnit\Framework\TestCase;

class SharedTokenAuthenticationTest extends TestCase
{
    protected $middleware;

    public function setUp()
    {
        $this->middleware = new SharedTokenAuthentication('secret');
    }

    public function test_build_middleware()
    {
        $this->assertInstanceOf(SharedTokenAuthentication::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }
}