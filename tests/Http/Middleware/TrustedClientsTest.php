<?php

namespace Rubix\Server\Tests\Http\Middleware;

use Rubix\Server\Http\Middleware\Middleware;
use Rubix\Server\Http\Middleware\TrustedClients;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * @group Middleware
 * @covers \Rubix\Server\Http\Middleware\TrustedClients
 */
class TrustedClientsTest extends TestCase
{
    /**
     * @var \Rubix\Server\Http\Middleware\TrustedClients
     */
    protected $middleware;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->middleware = new TrustedClients([
            '127.0.0.1', '192.168.4.1', '45.63.67.15',
        ]);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(TrustedClients::class, $this->middleware);
        $this->assertInstanceOf(Middleware::class, $this->middleware);
    }

    /**
     * @test
     */
    public function badIp() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $middleware = new TrustedClients([
            '127.0.0.1', 'bad', '45.63.67.15',
        ]);
    }
}
