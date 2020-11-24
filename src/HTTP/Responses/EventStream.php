<?php

namespace Rubix\Server\HTTP\Responses;

use React\Stream\ReadableStreamInterface;

class EventStream extends Success
{
    /**
     * @param \React\Stream\ReadableStreamInterface $stream
     */
    public function __construct(ReadableStreamInterface $stream)
    {
        parent::__construct([
            'Content-Type' => 'text/event-stream',
            'Transfer-Encoding' => 'chunked',
            'Cache-Control' => 'no-cache',
        ], $stream);
    }
}
