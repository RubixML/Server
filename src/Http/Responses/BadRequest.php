<?php

namespace Rubix\Server\Http\Responses;

class BadRequest extends Response
{
    /**
     * @param string[] $headers
     * @param string|null|\React\Stream\ReadableStreamInterface|\Psr\Http\Message\StreamInterface $data
     */
    public function __construct(array $headers, $data = null)
    {
        parent::__construct(400, $headers, $data);
    }
}
