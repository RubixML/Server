<?php

namespace Rubix\Server\Services;

use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use ArrayAccess;

use function class_exists;
use function is_callable;

/**
 * @implements ArrayAccess<string, array>
 */
class EventMapping implements ArrayAccess
{
    /**
     * The events and their handlers.
     *
     * @var array[]
     */
    protected $mapping;

    /**
     * Subscribe an array of listeners to their events.
     *
     * @param \Rubix\Server\Listeners\Listener[] $listeners
     * @return self
     */
    public static function subscribe(array $listeners) : self
    {
        $mapping = [];

        foreach ($listeners as $listener) {
            foreach ($listener->events() as $class => $handlers) {
                foreach ($handlers as $handler) {
                    $mapping[$class][] = $handler;
                }
            }
        }

        return new self($mapping);
    }

    /**
     * @param array[] $mapping
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(array $mapping)
    {
        foreach ($mapping as $class => $handlers) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Class $class does not exist.");
            }

            foreach ($handlers as $handler) {
                if (!is_callable($handler)) {
                    throw new InvalidArgumentException('Handler must be callable.');
                }
            }
        }

        $this->mapping = $mapping;
    }

    /**
     * Return an array of handlers for an event class.
     *
     * @param string $class
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     * @return mixed[]
     */
    public function offsetGet($class) : array
    {
        if (isset($this->mapping[$class])) {
            return $this->mapping[$class];
        }

        throw new InvalidArgumentException("Event $class not found.");
    }

    /**
     * @param string $class
     * @param mixed[] $handlers
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function offsetSet($class, $handlers) : void
    {
        throw new RuntimeException('Mapping cannot be mutated directly.');
    }

    /**
     * Does an event exist in the mapping.
     *
     * @param string $class
     * @return bool
     */
    public function offsetExists($class) : bool
    {
        return isset($this->mapping[$class]);
    }

    /**
     * @param string $class
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function offsetUnset($class) : void
    {
        throw new RuntimeException('Mapping cannot be mutated directly.');
    }
}
