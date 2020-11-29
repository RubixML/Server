<?php

namespace Rubix\Server\HTTP\Middleware\Client;

interface Middleware
{
    /**
     * Return the higher-order function.
     *
     * @return callable
     */
    public function __invoke() : callable;
}
