<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Handlers\Handler;
use Rubix\Server\Commands\ProbaSample;
use Rubix\Server\Handlers\ProbaSampleHandler;
use Rubix\Server\Responses\ProbaSampleResponse;
use Rubix\ML\Classifiers\GaussianNB;
use PHPUnit\Framework\TestCase;

class ProbaSampleHandlerTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    protected const EXPECTED_PROBABILITIES = [
        'not monster' => 0.8,
        'monster' => 0.2,
    ];
    
    protected $handler;

    public function setUp()
    {
        $estimator = $this->createMock(GaussianNB::class);

        $estimator->method('probaSample')
            ->willReturn(self::EXPECTED_PROBABILITIES);

        $this->handler = new ProbaSampleHandler($estimator);
    }

    public function test_build_handler()
    {
        $this->assertInstanceOf(ProbaSampleHandler::class, $this->handler);
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    public function test_handle_command()
    {
        $response = $this->handler->handle(new ProbaSample(self::SAMPLE));

        $this->assertInstanceOf(ProbaSampleResponse::class, $response);
        $this->assertEquals(self::EXPECTED_PROBABILITIES, $response->probabilities());
    }
}
