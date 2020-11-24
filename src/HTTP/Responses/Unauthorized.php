<?php

namespace Rubix\Server\HTTP\Responses;

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
