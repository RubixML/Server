<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;

/**
 * Gzip
 *
 * A compression format based on the DEFLATE algorithm with a header and checksum.
 *
 * References:
 * [1] P. Deutsch. (1996). RFC 1951 - DEFLATE Compressed Data Format Specification
 * version.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class Gzip implements Serializer
{
    /**
     * The compression level between 0 and 9, 0 meaning no compression.
     *
     * @var int
     */
    protected $level;

    /**
     * The base serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * @param int $level
     * @param \Rubix\Server\Serializers\Serializer|null $serializer
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(int $level = 1, ?Serializer $serializer = null)
    {
        if ($level < 0 or $level > 9) {
            throw new InvalidArgumentException('Level must be'
                . " between 0 and 9, $level given.");
        }

        if ($serializer instanceof self) {
            throw new InvalidArgumentException('Base serializer'
                . ' must not be an instance of itself.');
        }

        $this->level = $level;
        $this->serializer = $serializer ?? new JSON();
    }

    /**
     * The HTTP headers to be send with each request or response in an associative array.
     *
     * @return string[]
     */
    public function headers() : array
    {
        return $this->serializer->headers() + [
            'Content-Encoding' => 'gzip',
            'Accept-Encoding' => 'gzip',
        ];
    }

    /**
     * Serialize a Message.
     *
     * @param \Rubix\Server\Message $message
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return string
     */
    public function serialize(Message $message) : string
    {
        $data = $this->serializer->serialize($message);

        $data = gzencode($data, $this->level);

        if ($data === false) {
            throw new RuntimeException('Failed to compress data.');
        }

        return $data;
    }

    /**
     * Unserialize a Message.
     *
     * @param string $data
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return \Rubix\Server\Message
     */
    public function unserialize(string $data) : Message
    {
        $data = gzdecode($data);

        if ($data === false) {
            throw new RuntimeException('Failed to decompress data.');
        }

        return $this->serializer->unserialize($data);
    }
}
