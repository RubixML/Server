<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;

interface Client
{
    /**
     * Send a command to the server and return the results.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @return array
     */
    public function send(Command $command) : array;
}