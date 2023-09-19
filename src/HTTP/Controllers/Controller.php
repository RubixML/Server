<?php

namespace Rubix\Server\HTTP\Controllers;

abstract class Controller
{
    /**
     * Return the routes this controller handles.
     *
     * @return array<mixed>
     */
    abstract public function routes() : array;
}
