<?php

namespace Rubix\Server\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller
{    
    /**
     * Handle the request.
     * 
     * @param  Request  $request
     * @param  array  $params
     * @return Response
     */
    abstract public function handle(Request $request, array $params) : Response;

    /**
     * Allow an instance to be called like a function.
     * 
     * @param  Request  $request
     * @param  array  $params
     * @return Response
     */
    public function __invoke(Request $request, array $params) : Response
    {
        return $this->handle($request, $params);
    }
}