<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\ServerStatusResponse;
use PHPUnit\Framework\TestCase;

class ServerStatusResponseTest extends TestCase
{
    protected const REQUESTS = [
        'count' => 10,
        'requests_min' => 6.2,
    ];

    protected const MEMORY_USAGE = [
        'current' => 250,
        'peak' => 500,
    ];

    protected const UPTIME = 999;

    /**
     * @var \Rubix\Server\Responses\ServerStatusResponse
     */
    protected $response;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->response = new ServerStatusResponse(self::REQUESTS, self::MEMORY_USAGE, self::UPTIME);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ServerStatusResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * @test
     */
    public function asArray() : void
    {
        $expected = [
            'requests' => self::REQUESTS,
            'memory_usage' => self::MEMORY_USAGE,
            'uptime' => self::UPTIME,
        ];

        $payload = $this->response->asArray();

        $this->assertIsArray($payload);
        $this->assertEquals($expected, $payload);
    }
}
