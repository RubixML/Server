<?php

namespace Rubix\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Middleware
{
    /**
     * Run the middleware over the request.
     * 
     * @param  Request  $request
     * @param  callable  $next
     * @return Response
     */
    abstract public function handle(Request $request, callable $next) : Response;

    /**
     * Allow an instance to be called like a function.
     * 
     * @param  Request  $request
     * @param  callable  $next
     * @return Response
     */
    public function __invoke(Request $request, callable $next) : Response
    {
        return $this->handle($request, $next);
    }
}