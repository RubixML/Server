<?php

namespace Rubix\Server\Http\Responses;

class UnprocessableEntity extends Response
{
    /**
     * @param string[] $headers
     * @param string|null|\React\Stream\ReadableStreamInterface|\Psr\Http\Message\StreamInterface $data
     */
    public function __construct(array $headers, $data = null)
    {
        parent::__construct(422, $headers, $data);
    }
}
