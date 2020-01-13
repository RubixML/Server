<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;
use RuntimeException;

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
     * @throws \RuntimeException
     */
    public function __construct()
    {
        if (!extension_loaded('igbinary')) {
            throw new RuntimeException('Igbinary extension is not loaded,'
                . ' check PHP configuration.');
        }
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
     * @return \Rubix\Server\Message;
     */
    public function unserialize(string $data) : Message
    {
        return igbinary_unserialize($data);
    }
}
