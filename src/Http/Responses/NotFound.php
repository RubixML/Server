<?php

namespace Rubix\Server\Http\Responses;

class NotFound extends Response
{
    public function __construct()
    {
        parent::__construct(404);
    }
}
