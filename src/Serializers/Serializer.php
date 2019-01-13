<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Commands\Command;

interface Serializer
{
    /**
     * Serialize a command.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @return string
     */
    public function serialize(Command $command) : string;

    /**
     * Unserialize a command.
     * 
     * @param string  $data
     * @return \Rubix\Server\Commands\Command;
     */
    public function unserialize(string $data) : Command;
}