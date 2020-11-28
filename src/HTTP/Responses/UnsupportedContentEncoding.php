<?php

namespace Rubix\Server\HTTP\Responses;

class UnsupportedContentEncoding extends Response
{
    /**
     * @param string[] $acceptedContentEncodings
     */
    public function __construct(array $acceptedContentEncodings)
    {
        parent::__construct(415, [
            'Accept-Encoding' => implode(', ', $acceptedContentEncodings),
        ]);
    }
}
