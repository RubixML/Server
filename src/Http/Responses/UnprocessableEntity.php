<?php

namespace Rubix\Server\Http\Responses;

class UnprocessableEntity extends Response
{
    /**
     * @param string[] $headers
     * @param string $data
     */
    public function __construct(array $headers, string $data)
    {
        parent::__construct(422, $headers, $data);
    }
}
