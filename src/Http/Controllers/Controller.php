<?php

namespace Rubix\Server\Http\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;

interface Controller
{
    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(Request $request);
}
