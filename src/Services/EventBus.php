<?php

namespace Rubix\Server\Services;

use Rubix\Server\Exceptions\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Exception;

use function get_class;
use function class_exists;
use function is_callable;

/**
 * Event Bus
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class EventBus
{
    /**
     * The mapping of events to their listeners.
     *
     * @var callable[]
     */
    protected $listeners;

    /**
     * @param array[] $listeners
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(array $listeners)
    {
        foreach ($listeners as $class => $handlers) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Class $class does not exist.");
            }

            foreach ($handlers as $handler) {
                if (!is_callable($handler)) {
                    throw new InvalidArgumentException('Handler must be callable.');
                }
            }
        }

        $this->listeners = $listeners;
    }

    /**
     * Emit an event and call any handlers listening for it.
     *
     * @param \Rubix\Server\Events\Event $event
     * @throws \Rubix\Server\Exceptions\ListenerNotFound
     */
    public function emit(Event $event) : void
    {
        $class = get_class($event);

        if (isset($this->listeners[$class])) {
            $handlers = $this->listeners[$class];

            foreach ($handlers as $handler) {
                $handler($event);
            }
        }
    }
}
