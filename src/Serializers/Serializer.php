<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;

interface Serializer
{
    /**
     * Return the MIME type of the encoding.
     *
     * @return string
     */
    public function mime() : string;

    /**
     * The HTTP headers to be send with each request or response.
     *
     * @return string[]
     */
    public function headers() : array;

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
     * @return \Rubix\Server\Message
     */
    public function unserialize(string $data) : Message;
}
