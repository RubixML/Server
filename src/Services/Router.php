<?php

namespace Rubix\Server\Services;

use Rubix\Server\Http\Responses\NotFound;
use Rubix\Server\Http\Responses\MethodNotAllowed;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Router
{
    /**
     * The mapping of URIs to their method/controller pairs.
     *
     * @var \Rubix\Server\Services\Routes
     */
    protected $schema;

    /**
     * @param \Rubix\Server\Services\Routes $schema
     */
    public function __construct(Routes $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Dispatch the request to a controller and return an immediate or deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function dispatch(Request $request)
    {
        $path = $request->getUri()->getPath();

        if (empty($this->schema[$path])) {
            return new NotFound();
        }

        $actions = $this->schema[$path];

        $method = $request->getMethod();

        if (empty($actions[$method])) {
            return new MethodNotAllowed(array_keys($actions));
        }

        return $actions[$method]($request);
    }
}
