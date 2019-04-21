<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;
use RuntimeException;

class JSON implements Serializer
{
    /**
     * Serialize a message.
     *
     * @param \Rubix\Server\Message $message
     * @return string
     */
    public function serialize(Message $message) : string
    {
        return json_encode($message) ?: '';
    }

    /**
     * Unserialize a message.
     *
     * @param string $data
     * @throws \RuntimeException
     * @return \Rubix\Server\Message;
     */
    public function unserialize(string $data) : Message
    {
        $json = json_decode($data, true);

        if (isset($json['name']) and class_exists($json['name'])) {
            return $json['name']::fromArray($json['data'] ?? []);
        }

        throw new RuntimeException('Message could not be'
            . ' reconstituted.');
    }
}
