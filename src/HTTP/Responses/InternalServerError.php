<?php

namespace Rubix\Server\HTTP\Responses;

class InternalServerError extends Response
{
    public function __construct()
    {
        parent::__construct(500);
    }
}
