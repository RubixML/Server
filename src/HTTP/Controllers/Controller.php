<?php

namespace Rubix\Server\HTTP\Controllers;

interface Controller
{
    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array;
}
