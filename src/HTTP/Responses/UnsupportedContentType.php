<?php

namespace Rubix\Server\HTTP\Responses;

class UnsupportedContentType extends Response
{
    /**
     * @param string[] $acceptedContentTypes
     */
    public function __construct(array $acceptedContentTypes)
    {
        parent::__construct(415, [
            'Accept' => implode(', ', $acceptedContentTypes),
        ]);
    }
}
