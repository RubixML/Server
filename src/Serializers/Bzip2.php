<?php

namespace Rubix\Server\Serializers;

use Rubix\Server\Message;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;

/**
 * Bzip2
 *
 * A compression format based on the Burrows–Wheeler algorithm. Bzip2 is slightly smaller than
 * Gzip but is slower and requires more memory.
 *
 * References:
 * [1] J. Tsai. (2006). Bzip2: Format Specification
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class Bzip2 implements Serializer
{
    /**
     * The size of each block between 1 and 9 where 9 gives the best compression.
     *
     * @var int
     */
    protected $blockSize;

    /**
     * Controls how the compression phase behaves when the input is highly repetitive.
     *
     * @var int
     */
    protected $workFactor;

    /**
     * The base serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * @param int $blockSize
     * @param int $workFactor
     * @param \Rubix\Server\Serializers\Serializer|null $serializer
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(int $blockSize = 4, int $workFactor = 0, ?Serializer $serializer = null)
    {
        if (!extension_loaded('bz2')) {
            throw new RuntimeException('Bzip2 extension is not'
                . ' loaded, check PHP configuration.');
        }

        if ($blockSize < 1 or $blockSize > 9) {
            throw new InvalidArgumentException('Block size must'
                . " be between 0 and 9, $blockSize given.");
        }

        if ($serializer instanceof self) {
            throw new InvalidArgumentException('Base serializer'
                . ' must not be an instance of itself.');
        }

        $this->blockSize = $blockSize;
        $this->workFactor = $workFactor;
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
            'Content-Encoding' => 'bzip2',
            'Accept-Encoding' => 'bzip2',
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

        $data = bzcompress($data, $this->blockSize, $this->workFactor);

        if (!is_string($data)) {
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
        $data = bzdecompress($data);

        if (!is_string($data)) {
            throw new RuntimeException('Failed to decompress data.');
        }

        return $this->serializer->unserialize($data);
    }
}
