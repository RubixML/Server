<?php

namespace Rubix\Server\Services;

use Rubix\Server\Listeners\Listener;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;
use ArrayAccess;

use function class_exists;
use function is_callable;

/**
 * @implements ArrayAccess<string, array>
 */
class Subscriptions implements ArrayAccess
{
    /**
     * The mapping of events to their handlers.
     *
     * @var array<array<\Rubix\Server\Listeners\Listener|callable>>
     */
    protected array $subscriptions;

    /**
     * Subscribe an array of listeners to their events.
     *
     * @param array<\Rubix\Server\Listeners\Listener|callable> $listeners
     * @throws \InvalidArgumentException
     * @return self
     */
    public static function subscribe(array $listeners) : self
    {
        $subscriptions = [];

        foreach ($listeners as $listener) {
            if (!$listener instanceof Listener) {
                throw new InvalidArgumentException('Listener must implement'
                    . ' the Listener interface.');
            }

            foreach ($listener->events() as $class => $handlers) {
                foreach ($handlers as $handler) {
                    $subscriptions[$class][] = $handler;
                }
            }
        }

        return new self($subscriptions);
    }

    /**
     * @param array<array<\Rubix\Server\Listeners\Listener|callable>> $subscriptions
     * @throws InvalidArgumentException
     */
    public function __construct(array $subscriptions)
    {
        foreach ($subscriptions as $class => $handlers) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Class $class does not exist.");
            }

            foreach ($handlers as $handler) {
                if (!is_callable($handler)) {
                    throw new InvalidArgumentException('Handler must be callable.');
                }
            }
        }

        $this->subscriptions = $subscriptions;
    }

    /**
     * Return an array of handlers for an event class.
     *
     * @param string $class
     * @throws InvalidArgumentException
     * @return mixed[]
     */
    public function offsetGet($class) : array
    {
        if (isset($this->subscriptions[$class])) {
            return $this->subscriptions[$class];
        }

        throw new InvalidArgumentException("Event $class not found.");
    }

    /**
     * @param string $class
     * @param mixed[] $handlers
     * @throws RuntimeException
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
        return isset($this->subscriptions[$class]);
    }

    /**
     * @param string $class
     * @throws RuntimeException
     */
    public function offsetUnset($class) : void
    {
        throw new RuntimeException('Mapping cannot be mutated directly.');
    }
}
