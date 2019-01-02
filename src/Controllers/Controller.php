<?php

namespace Rubix\Server\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;

interface Controller
{
    const HEADERS = [
        'Content-Type' => 'text/json',
    ];
    
    /**
     * Handle the request.
     * 
     * @param  Request  $request
     * @param  array  $params
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request, array $params) : ResponseInterface;
}