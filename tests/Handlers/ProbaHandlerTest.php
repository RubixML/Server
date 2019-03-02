<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\Proba;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\ProbaResponse;
use Rubix\ML\Classifiers\GaussianNB;
use PHPUnit\Framework\TestCase;

class ProbaHandlerTest extends TestCase
{
    protected const SAMPLES = [
        ['mean', 'rough', 'loner'],
    ];

    protected const EXPECTED_PROBABILITIES = [
        [
            'not monster' => 0.8,
            'monster' => 0.2,
        ],
    ];

    protected $command;
    
    protected $handler;

    public function setUp()
    {
        $estimator = $this->createMock(GaussianNB::class);

        $estimator->method('proba')
            ->willReturn(self::EXPECTED_PROBABILITIES);

        $this->command = new Proba(self::SAMPLES);

        $this->handler = new ProbaHandler($estimator);
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(ProbaHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command()
    {
        $response = $this->handler->handle($this->command);

        $this->assertInstanceOf(ProbaResponse::class, $response);
        $this->assertEquals(self::EXPECTED_PROBABILITIES, $response->probabilities());
    }
}
