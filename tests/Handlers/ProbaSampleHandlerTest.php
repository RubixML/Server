<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\Server\Commands\ProbaSample;
use Rubix\Server\Handlers\ProbaSampleHandler;
use Rubix\Server\Payloads\ProbaSamplePayload;
use Rubix\ML\Classifiers\GaussianNB;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\ProbaSampleHandler
 */
class ProbaSampleHandlerTest extends TestCase
{
    protected const SAMPLE = ['nice', 'rough', 'loner'];

    protected const EXPECTED_PROBABILITIES = [
        'not monster' => 0.8,
        'monster' => 0.2,
    ];

    /**
     * @var \Rubix\Server\Handlers\ProbaSampleHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $estimator = $this->createMock(GaussianNB::class);

        $estimator->method('probaSample')
            ->willReturn(self::EXPECTED_PROBABILITIES);

        $this->handler = new ProbaSampleHandler($estimator);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ProbaSampleHandler::class, $this->handler);
        $this->assertIsCallable($this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $command = new ProbaSample(self::SAMPLE);

        $payload = call_user_func($this->handler, $command);

        $this->assertInstanceOf(ProbaSamplePayload::class, $payload);
        $this->assertEquals(self::EXPECTED_PROBABILITIES, $payload->probabilities());
    }
}
