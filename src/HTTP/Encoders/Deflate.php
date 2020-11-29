<?php

namespace Rubix\Server\HTTP\Encoders;

use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;

class Deflate implements Encoder
{
    /**
     * The compression level between 0 and 9 with 0 meaning no compression.
     *
     * @var int
     */
    protected $level;

    /**
     * @param int $level
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(int $level = 1)
    {
        if ($level < 0 or $level > 9) {
            throw new InvalidArgumentException('Level must be'
                . " between 0 and 9, $level given.");
        }

        $this->level = $level;
    }

    /**
     * Return the compression scheme.
     *
     * @return string
     */
    public function scheme() : string
    {
        return 'deflate';
    }

    /**
     * Encode the data.
     *
     * @param string $data
     * @return string
     */
    public function encode(string $data) : string
    {
        $data = gzdeflate($data, $this->level);

        if ($data === false) {
            throw new RuntimeException('Could not encode data.');
        }

        return $data;
    }
}
