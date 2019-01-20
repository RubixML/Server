<?php

namespace Rubix\Server\Tests\Responses;

use Rubix\Server\Responses\Response;
use Rubix\Server\Responses\ProbaResponse;
use PHPUnit\Framework\TestCase;

class ProbaResponseTest extends TestCase
{
    protected $response;

    public function setUp()
    {
        $this->response = new ProbaResponse([
            [
                'monster' => 0.4,
                'not monster' => 0.6,
            ],
        ]);
    }

    public function test_build_response()
    {
        $this->assertInstanceOf(ProbaResponse::class, $this->response);
        $this->assertInstanceOf(Response::class, $this->response);
    }
}