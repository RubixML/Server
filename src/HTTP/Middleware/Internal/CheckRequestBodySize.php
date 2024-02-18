<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Rubix\Server\HTTP\Responses\PayloadTooLarge;
use Psr\Http\Message\ServerRequestInterface;

class CheckRequestBodySize
{
    /**
     * The maximum size of a request body in bytes.
     *
     * @var int
     */
    protected int $postMaxSize;

    /**
     * @param int $postMaxSize
     */
    public function __construct(int $postMaxSize)
    {
        $this->postMaxSize = $postMaxSize;
    }

    /**
     * Dispatch events related to the request/response cycle.
     *
     * @internal
     *
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($request->hasHeader('Content-Length')) {
            $size = (int) $request->getHeaderLine('Content-Length');

            if ($size > $this->postMaxSize) {
                return new PayloadTooLarge();
            }
        }

        return $next($request);
    }
}
