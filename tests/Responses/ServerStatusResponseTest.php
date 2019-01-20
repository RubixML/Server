<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\ServerStatusResponse;
use PHPUnit\Framework\TestCase;

class ServerStatusResponseTest extends TestCase
{
    protected $response;

    public function setUp()
    {
        $this->response = new ServerStatusResponse([
            'count' => 10,
            'requests_min' => 6.2,
        ], [
            'current' => 250,
            'peak' => 500,
        ], 100);
    }

    public function test_build_response()
    {
        $this->assertInstanceOf(ServerStatusResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }
}