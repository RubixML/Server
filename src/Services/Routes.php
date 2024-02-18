<?php

namespace Rubix\Server\Services;

use Rubix\Server\HTTP\Controllers\Controller;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

use ArrayAccess;

use function in_array;
use function is_string;
use function is_array;
use function is_callable;

/**
 * @implements ArrayAccess<string, array>
 */
class Routes implements ArrayAccess
{
    public const SUPPORTED_METHODS = [
        'GET', 'HEAD', 'POST',
    ];

    /**
     * The routes and their controllers.
     *
     * @var array<array<\Rubix\Server\HTTP\Controllers\Controller>>
     */
    protected array $routes;

    /**
     * Collect the routes from an array of controllers.
     *
     * @param \Rubix\Server\HTTP\Controllers\Controller[] $controllers
     * @throws InvalidArgumentException
     * @return self
     */
    public static function collect(array $controllers) : self
    {
        $routes = [];

        foreach ($controllers as $controller) {
            if (!$controller instanceof Controller) {
                throw new InvalidArgumentException('Controller must implement'
                    . ' the Controller interface.');
            }

            foreach ($controller->routes() as $path => $actions) {
                foreach ($actions as $method => $handler) {
                    if (is_array($handler) and !is_callable($handler)) {
                        $next = null;

                        while ($current = array_pop($handler)) {
                            $next = function (ServerRequestInterface $request) use ($current, $next) {
                                return $current($request, $next);
                            };
                        }

                        $handler = $next;
                    }

                    $routes[$path][$method] = $handler;
                }
            }
        }

        return new self($routes);
    }

    /**
     * @param array<array<\Rubix\Server\HTTP\Controllers\Controller>> $routes
     * @throws InvalidArgumentException
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

                if (!is_callable($controller)) {
                    throw new InvalidArgumentException('Controller must be callable.');
                }
            }
        }

        $this->routes = $routes;
    }

    /**
     * Return a route from the schema.
     *
     * @param string $path
     * @throws InvalidArgumentException
     * @return mixed[]
     */
    public function offsetGet($path) : array
    {
        if (isset($this->routes[$path])) {
            return $this->routes[$path];
        }

        throw new InvalidArgumentException("Route $path not found.");
    }

    /**
     * @param string $path
     * @param mixed[] $actions
     * @throws RuntimeException
     */
    public function offsetSet($path, $actions) : void
    {
        throw new RuntimeException('Schema cannot be mutated directly.');
    }

    /**
     * Does a route exist in the schema.
     *
     * @param string $path
     * @return bool
     */
    public function offsetExists($path) : bool
    {
        return isset($this->routes[$path]);
    }

    /**
     * @param string $path
     * @throws RuntimeException
     */
    public function offsetUnset($path) : void
    {
        throw new RuntimeException('Schema cannot be mutated directly.');
    }
}
