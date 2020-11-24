<?php

namespace Rubix\Server\Services;

use Rubix\Server\HTTP\Responses\NotFound;
use Rubix\Server\HTTP\Responses\MethodNotAllowed;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Router
{
    /**
     * The mapping of URIs to their method/controller pairs.
     *
     * @var \Rubix\Server\Services\Routes
     */
    protected $routes;

    /**
     * @param \Rubix\Server\Services\Routes $routes
     */
    public function __construct(Routes $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Return the routing schema.
     *
     * @return \Rubix\Server\Services\Routes
     */
    public function routes() : Routes
    {
        return $this->routes;
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

        if (empty($this->routes[$path])) {
            return new NotFound();
        }

        $actions = $this->routes[$path];

        $method = $request->getMethod();

        if (empty($actions[$method])) {
            return new MethodNotAllowed(array_keys($actions));
        }

        return $actions[$method]($request);
    }
}
