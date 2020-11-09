<?php

namespace Rubix\Server\Http\Responses;

class Unauthorized extends Response
{
    /**
     * @param string $realm
     */
    public function __construct(string $realm)
    {
        parent::__construct(401, [
            'WWW-Authenticate' => "Basic realm=$realm",
        ]);
    }
}
