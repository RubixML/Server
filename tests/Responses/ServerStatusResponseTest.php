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

    protected $response;

    public function setUp()
    {
        $this->response = new ServerStatusResponse(self::REQUESTS, self::MEMORY_USAGE, self::UPTIME);
    }

    public function test_build_response()
    {
        $this->assertInstanceOf(ServerStatusResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function test_as_array()
    {
        $expected = [
            'requests' => self::REQUESTS,
            'memory_usage' => self::MEMORY_USAGE,
            'uptime' => self::UPTIME,
        ];
        
        $payload = $this->response->asArray();

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }
}
