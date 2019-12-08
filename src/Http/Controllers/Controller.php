<?php

namespace Rubix\Server\Http\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface Controller
{
    public const OK = 200;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;
    public const INTERNAL_SERVER_ERROR = 500;

    /**
     * Handle the request.
     *
     * @param Request $request
     * @param mixed[]|null $params
     * @return Response
     */
    public function handle(Request $request, ?array $params = null) : Response;
}
