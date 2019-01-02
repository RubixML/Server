<?php

namespace Rubix\Server;

interface Server
{
    /**
     * Boot up the server.
     * 
     * @return void
     */
    public function run() : void;
}