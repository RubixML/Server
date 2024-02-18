<?php

namespace Rubix\Server\HTTP\Middleware;

use Psr\Http\Message\ServerRequestInterface;

interface Middleware
{
    /**
     * Process the request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next);
}
