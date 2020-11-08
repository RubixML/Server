<?php

namespace Rubix\Server\Http;

use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use React\Promise\Promise;

use function in_array;
use function is_string;

class Router
{
    public const SUPPORTED_METHODS = [
        'OPTIONS', 'GET', 'HEAD', 'POST',
    ];

    /**
     * The mapping of URIs to their method/controller pairs.
     *
     * @var array[]
     */
    protected $routes;

    /**
     * @param array[] $routes
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(array $routes)
    {
        foreach ($routes as $path => $actions) {
            if (!is_string($path)) {
                throw new InvalidArgumentException('Path must be a string.');
            }

            foreach ($actions as $method => $controller) {
                if (!in_array($method, self::SUPPORTED_METHODS)) {
                    throw new InvalidArgumentException('HTTP method not supported.');
                }

                if (!$controller instanceof Controller) {
                    throw new InvalidArgumentException('Controller must'
                        . ' implement the Controller interface.');
                }
            }
        }

        $this->routes = $routes;
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
            return new ReactResponse(NOT_FOUND);
        }

        $actions = $this->routes[$path];

        $method = $request->getMethod();

        if (empty($actions[$method])) {
            return new ReactResponse(METHOD_NOT_ALLOWED, [
                'Allowed' => implode(', ', array_keys($actions)),
            ]);
        }

        $controller = $actions[$method];

        return new Promise(function ($resolve) use ($controller, $request) {
            $resolve($controller->handle($request));
        });
    }
}
