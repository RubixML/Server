<?php

namespace Rubix\Server\Services;

use Rubix\Server\Handlers\Handler;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use ArrayAccess;

use function class_exists;
use function is_callable;

/**
 * @implements ArrayAccess<string, callable>
 */
class Bindings implements ArrayAccess
{
    /**
     * The query/handler bindings.
     *
     * @var callable[]
     */
    protected $bindings;

    /**
     * Bind queries to their handlers.
     *
     * @param (\Rubix\Server\Handlers\Handler|null)[] $handlers
     * @return self
     */
    public static function bind(array $handlers) : self
    {
        $bindings = [];

        foreach ($handlers as $handler) {
            if ($handler instanceof Handler) {
                foreach ($handler->queries() as $class => $handler) {
                    $bindings[$class] = $handler;
                }
            }
        }

        return new self($bindings);
    }

    /**
     * @param callable[] $bindings
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(array $bindings)
    {
        foreach ($bindings as $class => $handler) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Class $class does not exist.");
            }

            if (!is_callable($handler)) {
                throw new InvalidArgumentException('Handler must be callable.');
            }
        }

        $this->bindings = $bindings;
    }

    /**
     * Return an array of handlers for an event class.
     *
     * @param string $class
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     * @return callable
     */
    public function offsetGet($class) : callable
    {
        if (isset($this->bindings[$class])) {
            return $this->bindings[$class];
        }

        throw new InvalidArgumentException("Query $class not found.");
    }

    /**
     * @param string $class
     * @param mixed[] $handlers
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function offsetSet($class, $handlers) : void
    {
        throw new RuntimeException('Binding cannot be mutated directly.');
    }

    /**
     * Does an event exist in the mapping.
     *
     * @param string $class
     * @return bool
     */
    public function offsetExists($class) : bool
    {
        return isset($this->bindings[$class]);
    }

    /**
     * @param string $class
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function offsetUnset($class) : void
    {
        throw new RuntimeException('Binding cannot be mutated directly.');
    }
}
