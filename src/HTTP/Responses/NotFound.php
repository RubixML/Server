<?php

namespace Rubix\Server\HTTP\Responses;

class NotFound extends Response
{
    public function __construct()
    {
        parent::__construct(404);
    }
}
