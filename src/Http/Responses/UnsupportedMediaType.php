<?php

namespace Rubix\Server\HTTP\Responses;

class UnsupportedMediaType extends Response
{
    /**
     * @param string $acceptedContentType
     */
    public function __construct(string $acceptedContentType)
    {
        parent::__construct(415, [
            'Accept' => $acceptedContentType,
        ]);
    }
}
