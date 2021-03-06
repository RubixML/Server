<?php

namespace Rubix\Server\HTTP\Responses;

class MethodNotAllowed extends Response
{
    /**
     * @param string[] $allowedMethods
     */
    public function __construct(array $allowedMethods)
    {
        parent::__construct(405, [
            'Allowed' => implode(', ', $allowedMethods),
        ]);
    }
}
