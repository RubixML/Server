<?php

namespace Rubix\Server\HTTP\Responses;

class Success extends Response
{
    /**
     * @param string[] $headers
     * @param string|null|\React\Stream\ReadableStreamInterface|\Psr\Http\Message\StreamInterface $data
     */
    public function __construct(array $headers, $data = '')
    {
        parent::__construct(200, $headers, $data);
    }
}
