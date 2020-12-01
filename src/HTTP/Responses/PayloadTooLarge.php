<?php

namespace Rubix\Server\HTTP\Responses;

class PayloadTooLarge extends Response
{
    public function __construct()
    {
        parent::__construct(413);
    }
}
