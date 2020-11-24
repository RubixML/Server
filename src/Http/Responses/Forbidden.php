<?php

namespace Rubix\Server\HTTP\Responses;

class Forbidden extends Response
{
    public function __construct()
    {
        parent::__construct(403);
    }
}
