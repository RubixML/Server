<?php

namespace Rubix\Server\Http;

use Rubix\Server\Http\Responses\NotFound;
use Rubix\Server\Http\Responses\MethodNotAllowed;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Router
{
    /**
     * The mapping of URIs to their method/controller pairs.
     *
     * @var \Rubix\Server\Http\RoutingSchema
     */
    protected $schema;

    /**
     * @param \Rubix\Server\Http\RoutingSchema $schema
     */
    public function __construct(RoutingSchema $schema)
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
