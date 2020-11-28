<?php

namespace Rubix\Server\HTTP\Responses;

class NotImplemented extends Response
{
    public function __construct()
    {
        parent::__construct(501);
    }
}
