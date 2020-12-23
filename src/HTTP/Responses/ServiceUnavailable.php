<?php

namespace Rubix\Server\HTTP\Responses;

class ServiceUnavailable extends Response
{
    public function __construct()
    {
        parent::__construct(503);
    }
}
