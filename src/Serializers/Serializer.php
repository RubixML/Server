<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;

interface Serializer
{
    /**
     * Serialize a message.
     *
     * @param \Rubix\Server\Message $message
     * @return string
     */
    public function serialize(Message $message) : string;

    /**
     * Unserialize a message.
     *
     * @param string $data
     * @return \Rubix\Server\Message;
     */
    public function unserialize(string $data) : Message;
}
