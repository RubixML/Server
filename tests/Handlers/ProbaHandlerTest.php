<?php

namespace Rubix\Server\Tests\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\Handler;
use Rubix\Server\Responses\ProbaResponse;
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
        $this->assertInstanceOf(Handler::class, $this->handler);
    }

    /**
     * @test
     */
    public function handle() : void
    {
        $response = $this->handler->handle(new Proba(new Unlabeled(self::SAMPLES)));

        $this->assertInstanceOf(ProbaResponse::class, $response);
        $this->assertEquals(self::EXPECTED_PROBABILITIES, $response->probabilities());
    }
}
