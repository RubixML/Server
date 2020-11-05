<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;
use Rubix\Server\Helpers\JSON as JSONHelper;
use Rubix\Server\Exceptions\RuntimeException;

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
        return JSONHelper::encode($message);
    }

    /**
     * Unserialize a message.
     *
     * @param string $data
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Message
     */
    public function unserialize(string $data) : Message
    {
        $json = JSONHelper::decode($data);

        if (isset($json['name']) and isset($json['data'])) {
            $class = $json['name'];

            if (class_exists($class)) {
                return $class::fromArray($json['data']);
            }
        }

        throw new RuntimeException('Message could not be reconstituted.');
    }
}
