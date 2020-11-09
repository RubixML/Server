<?php

namespace Rubix\Server\Http\Responses;

class InternalServerError extends Response
{
    public function __construct()
    {
        parent::__construct(500);
    }
}
