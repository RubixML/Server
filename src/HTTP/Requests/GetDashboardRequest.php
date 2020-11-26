<?php

namespace Rubix\Server\HTTP\Requests;

class GetDashboardRequest extends JSONRequest
{
    public function __construct()
    {
        parent::__construct('GET', '/server/dashboard');
    }
}
