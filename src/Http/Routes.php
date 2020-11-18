<?php

namespace Rubix\Server\Http;

use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use ArrayAccess;

use function in_array;
use function is_string;

/**
 * @implements ArrayAccess<string, array>
 */
class Routes implements ArrayAccess
{
    public const SUPPORTED_METHODS = [
        'OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE',
    ];

    /**
     * The routes and their controllers.
     *
     * @var array[]
     */
    protected $routes;

    /**
     * Collect the routes from an array of controllers.
     *
     * @param \Rubix\Server\Http\Controllers\Controller[] $controllers
     * @return self
     */
    public static function collect(array $controllers) : self
    {
        $routes = [];

        foreach ($controllers as $controller) {
            foreach ($controller->routes() as $path => $actions) {
                foreach ($actions as $method => $handler) {
                    $routes[$path][$method] = $handler;
                }
            }
        }

        return new self($routes);
    }

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
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
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
     * @throws \Rubix\Server\Exceptions\RuntimeException
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
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function offsetUnset($path) : void
    {
        throw new RuntimeException('Schema cannot be mutated directly.');
    }
}