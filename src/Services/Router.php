<?php

namespace Rubix\Server\Services;

use Rubix\Server\Http\Controllers\Controller;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;

use function in_array;
use function is_string;

use const Rubix\Server\Http\NOT_FOUND;
use const Rubix\Server\Http\METHOD_NOT_ALLOWED;

class Router
{
    public const AVAILABLE_METHODS = [
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
        foreach ($routes as $uri => $actions) {
            if (!is_string($uri)) {
                throw new InvalidArgumentException('URI must be a string.');
            }

            foreach ($actions as $method => $controller) {
                if (!in_array($method, self::AVAILABLE_METHODS)) {
                    throw new InvalidArgumentException('Invalid HTTP method.');
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
     * Dispatch the request to a controller and return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(Request $request) : Response
    {
        $uri = $request->getUri()->getPath();

        if (empty($this->routes[$uri])) {
            return new ReactResponse(NOT_FOUND);
        }

        $actions = $this->routes[$uri];

        $method = $request->getMethod();

        if (empty($actions[$method])) {
            return new ReactResponse(METHOD_NOT_ALLOWED, [
                'Allowed' => implode(', ', array_keys($actions)),
            ]);
        }

        $controller = $actions[$method];

        return $controller->handle($request);
    }
}
