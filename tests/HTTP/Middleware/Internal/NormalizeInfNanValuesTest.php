<?php

namespace Rubix\Server\Tests\HTTP\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Rubix\Server\HTTP\Middleware\Internal\ConvertRequestBodyConstants;
use PHPUnit\Framework\TestCase;

/**
 * @group Middleware
 * @covers \Rubix\Server\HTTP\Middleware\Internal\ConvertRequestBodyConstants
 */
class ConvertRequestBodyConstantsTest extends TestCase
{
    /**
     * @var \Rubix\Server\HTTP\Middleware\Internal\ConvertRequestBodyConstants
     */
    protected $middleware;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->middleware = new ConvertRequestBodyConstants();
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ConvertRequestBodyConstants::class, $this->middleware);
    }

    /**
     * @test
     */
    public function emptySamples() : void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn(['a' => 1]);
        $request->expects($this->never())->method('withParsedBody');

        $this->middleware->__invoke($request, function (ServerRequestInterface $request) {
            return $request;
        });
    }

    /**
     * @test
     */
    public function cleanSamples() : void
    {
        $body = [
            'samples' => [
                [1, 2, 3],
                [4, 5, 6],
            ],
        ];

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($body);
        $request->expects($this->once())->method('withParsedBody')->with($body)->willReturn($request);

        $this->middleware->__invoke($request, function (ServerRequestInterface $request) {
            return $request;
        });
    }

    /**
     * @test
     */
    public function infNanSamples() : void
    {
        $body = [
            'samples' => [
                [1, 'INF', 3],
                [4, 5, 'NAN'],
            ],
        ];

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($body);
        $request
            ->expects($this->once())
            ->method('withParsedBody')
            ->with($this->callback(function (array $body) {
                return  $body['samples'][0] === [1, INF, 3] &&
                    $body['samples'][1][0] == 4 &&
                    $body['samples'][1][1] == 5 &&
                    is_nan($body['samples'][1][2]);
            }))->willReturn($request);

        $this->middleware->__invoke($request, function (ServerRequestInterface $request) {
            return $request;
        });
    }
}
