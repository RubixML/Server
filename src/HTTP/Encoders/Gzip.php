<?php

namespace Rubix\Server\HTTP\Encoders;

use Rubix\Server\Exceptions\RuntimeException;

class Gzip extends Deflate
{
    /**
     * Return the compression scheme.
     *
     * @return string
     */
    public function scheme() : string
    {
        return 'gzip';
    }

    /**
     * Encode the data.
     *
     * @param string $data
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return string
     */
    public function encode(string $data) : string
    {
        $data = gzencode($data, $this->level);

        if ($data === false) {
            throw new RuntimeException('Could not encode data.');
        }

        return $data;
    }
}
