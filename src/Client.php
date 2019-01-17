<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use Rubix\Server\Responses\Response;

interface Client
{
    /**
     * Send a command to the server and return the results.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @return \Rubix\Server\Responses\Response
     */
    public function send(Command $command) : Response;
}