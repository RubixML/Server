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

    /**
     * Return the number of requests that have been received.
     * 
     * @var int
     */
    public function requests() : int;

    /**
     * Return the uptime of the server in seconds.
     * 
     * @return int
     */
    public function uptime() : int;
}