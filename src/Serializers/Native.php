<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;
use __PHP_Incomplete_Class;
use RuntimeException;

class Native implements Serializer
{
    /**
     * Serialize a Message.
     *
     * @param \Rubix\Server\Message $message
     * @return string
     */
    public function serialize(Message $message) : string
    {
        return serialize($message);
    }

    /**
     * Unserialize a Message.
     *
     * @param string $data
     * @return \Rubix\Server\Message
     */
    public function unserialize(string $data) : Message
    {
        $message = unserialize($data);

        if ($message === false) {
            throw new RuntimeException('Cannot read encoding, wrong'
                . ' format or corrupted data.');
        }

        if (!is_object($message)) {
            throw new RuntimeException('Unserialized encoding must'
                . ' be an object.');
        }

        if ($message instanceof __PHP_Incomplete_Class) {
            throw new RuntimeException('Missing class definition'
                . ' for unserialized object.');
        }

        if (!$message instanceof Message) {
            throw new RuntimeException('Unserialized object must'
                . ' be a message.');
        }

        return $message;
    }
}
