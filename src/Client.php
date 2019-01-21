<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use Rubix\Server\Responses\Response;

interface Client
{
    /**
     * Synchronously send a command to the server and return a response.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @return \Rubix\Server\Responses\Response
     */
    public function send(Command $command) : Response;
}