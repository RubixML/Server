<?php

namespace Rubix\Server\Http\Controllers;

interface Controller
{
    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array;
}
