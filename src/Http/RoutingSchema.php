<?php

namespace Rubix\Server\Http;

use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use ArrayAccess;

use function in_array;
use function is_string;

class RoutingSchema implements ArrayAccess
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
     * @param \Rubix\Server\Http\Controllers\Controller[]
     * @param array $controllers
     */
    public static function collect(array $controllers)
    {
        $routes = [];

        foreach ($controllers as $controller) {
            $routes += $controller->routes();
        }

        return new self($routes);
    }

    /**
     * @param \Rubix\Server\Http\RouteSchema $schema
     * @param array $routes
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
     * Return the routes in the schema.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return $this->routes;
    }

    /**
     * Return a route from the schema.
     *
     * @param string $path
     * @throws \Rubix\ML\Exceptions\InvalidArgumentException
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
     * @throws \Rubix\ML\Exceptions\RuntimeException
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
     * @throws \Rubix\ML\Exceptions\RuntimeException
     */
    public function offsetUnset($path) : void
    {
        throw new RuntimeException('Schema cannot be mutated directly.');
    }
}
