<?php

namespace Rubix\Server\Http\Responses;

class BadRequest extends Response
{
    /**
     * @param string[] $headers
     * @param string $data
     */
    public function __construct(array $headers, string $data)
    {
        parent::__construct(400, $headers, $data);
    }
}
