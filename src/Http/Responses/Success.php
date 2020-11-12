<?php

namespace Rubix\Server\Http\Responses;

class Success extends Response
{
    /**
     * @param string[] $headers
     * @param string $data
     */
    public function __construct(array $headers, string $data)
    {
        parent::__construct(200, $headers, $data);
    }
}
