<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;

class Native implements Serializer
{
    /**
     * Serialize a Message.
     * 
     * @param  \Rubix\Server\Message  $message
     * @return string
     */
    public function serialize(Message $message) : string
    {
        return serialize($message);
    }

    /**
     * Unserialize a Message.
     * 
     * @param string  $data
     * @return \Rubix\Server\Message;
     */
    public function unserialize(string $data) : Message
    {
        return unserialize($data);
    }
}