<?php

namespace Rubix\Server\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface Middleware
{
    /**
     * Process the request and return a response.
     *
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, callable $next) : Response;
}
