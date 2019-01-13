<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Commands\Command;

class Native implements Serializer
{
    /**
     * Serialize a command.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @return string
     */
    public function serialize(Command $command) : string
    {
        return serialize($command);
    }

    /**
     * Unserialize a command.
     * 
     * @param string  $data
     * @return \Rubix\Server\Commands\Command;
     */
    public function unserialize(string $data) : Command
    {
        return unserialize($data);
    }
}