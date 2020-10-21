<?php

namespace Rubix\Server\Http\Controllers;

use Psr\Http\Server\RequestHandlerInterface;

interface Controller extends RequestHandlerInterface
{
    public const OK = 200;

    public const NOT_FOUND = 404;

    public const METHOD_NOT_ALLOWED = 405;

    public const INTERNAL_SERVER_ERROR = 500;
}
