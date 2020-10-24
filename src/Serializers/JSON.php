<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;
use RuntimeException;

class JSON implements Serializer
{
    /**
     * The HTTP headers to be send with each request or response in an associative array.
     *
     * @return string[]
     */
    public function headers() : array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

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
     * @return \Rubix\Server\Message
     */
    public function unserialize(string $data) : Message
    {
        $json = json_decode($data, true);

        if (isset($json['name']) and isset($json['data'])) {
            $class = $json['name'];

            if (class_exists($class)) {
                return $class::fromArray($json['data']);
            }
        }

        throw new RuntimeException('Message could not be reconstituted.');
    }
}
