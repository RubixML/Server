<?php

namespace Rubix\Server\HTTP\Encoders;

interface Encoder
{
    /**
     * Return the compression scheme.
     *
     * @return string
     */
    public function scheme() : string;

    /**
     * Encode the data.
     *
     * @param string $data
     * @return string
     */
    public function encode(string $data) : string;
}
