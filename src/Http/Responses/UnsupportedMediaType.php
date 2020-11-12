<?php

namespace Rubix\Server\Http\Responses;

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
