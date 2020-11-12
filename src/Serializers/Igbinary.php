<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;
use __PHP_Incomplete_Class;
use Rubix\Server\Exceptions\RuntimeException;

/**
 * Igbinary
 *
 * Converts persistable object to and from a binary encoding. Igbinary format is
 * smaller and typically faster than plain text serializers.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class Igbinary implements Serializer
{
    /**
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function __construct()
    {
        if (!extension_loaded('igbinary')) {
            throw new RuntimeException('Igbinary extension is not loaded,'
                . ' check PHP configuration.');
        }
    }

    /**
     * Return the MIME type of the encoding.
     *
     * @return string
     */
    public function mime() : string
    {
        return 'application/octet-stream';
    }

    /**
     * The HTTP headers to be send with each request or response in an associative array.
     *
     * @return string[]
     */
    public function headers() : array
    {
        $mime = $this->mime();

        return [
            'Content-Type' => $mime,
            'Accept' => $mime,
        ];
    }

    /**
     * Serialize a Message.
     *
     * @param \Rubix\Server\Message $message
     * @return string
     */
    public function serialize(Message $message) : string
    {
        return igbinary_serialize($message) ?: '';
    }

    /**
     * Unserialize a Message.
     *
     * @param string $data
     * @return \Rubix\Server\Message
     */
    public function unserialize(string $data) : Message
    {
        $message = igbinary_unserialize($data);

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
