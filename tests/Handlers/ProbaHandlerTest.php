<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Queries\Proba;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Payloads\ProbaPayload;
use Rubix\ML\Classifiers\GaussianNB;
use PHPUnit\Framework\TestCase;

/**
 * @group Handlers
 * @covers \Rubix\Server\Handlers\ProbaHandler
 */
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

    /**
     * @var \Rubix\Server\Handlers\ProbaHandler
     */
    protected $handler;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $estimator = $this->createMock(GaussianNB::class);

        $estimator->method('proba')
            ->willReturn(self::EXPECTED_PROBABILITIES);

        $this->handler = new ProbaHandler($estimator);
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ProbaHandler::class, $this->handler);
        $this->assertIsCallable($this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $query = new Proba(new Unlabeled(self::SAMPLES));

        $payload = call_user_func($this->handler, $query);

        $this->assertInstanceOf(ProbaPayload::class, $payload);
        $this->assertEquals(self::EXPECTED_PROBABILITIES, $payload->probabilities());
    }
}
