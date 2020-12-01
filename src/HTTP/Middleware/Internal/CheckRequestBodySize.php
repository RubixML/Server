<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Rubix\Server\HTTP\Responses\PayloadTooLarge;
use Psr\Http\Message\ServerRequestInterface;

class CheckRequestBodySize
{
    /**
     * The maximum request body size.
     *
     * @var int
     */
    protected $maxBodySize;

    /**
     * @param int $maxBodySize
     */
    public function __construct(int $maxBodySize)
    {
        $this->maxBodySize = $maxBodySize;
    }

    /**
     * Dispatch events related to the request/response cycle.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($request->hasHeader('Content-Length')) {
            $size = (int) $request->getHeaderLine('Content-Length');

            if ($size > $this->maxBodySize) {
                return new PayloadTooLarge();
            }
        }

        return $next($request);
    }
}
