<?php

namespace Rubix\Server\Http\Responses;

class Forbidden extends Response
{
    public function __construct()
    {
        parent::__construct(403);
    }
}
