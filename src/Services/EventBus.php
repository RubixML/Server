<?php

namespace Rubix\Server\Services;

use Rubix\Server\Events\Event;
use Rubix\Server\Jobs\HandleEvent;
use Psr\Log\LoggerInterface;

use function get_class;

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
     * The job scheduler.
     *
     * @var Scheduler
     */
    protected Scheduler $scheduler;

    /**
     * A PSR-3 logger instance.
     *
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * The mapping of events to their listeners.
     *
     * @var Subscriptions|null
     */
    protected ?Subscriptions $subscriptions;

    /**
     * @param Scheduler $scheduler
     * @param LoggerInterface $logger
     */
    public function __construct(Scheduler $scheduler, LoggerInterface $logger)
    {
        $this->scheduler = $scheduler;
        $this->logger = $logger;
    }

    /**
     * Add the mapping of events to their listeners.
     *
     * @param Subscriptions $subscriptions
     */
    public function setSubscriptions(Subscriptions $subscriptions) : void
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * Return the mapping of events to their listeners.
     *
     * @return Subscriptions|null
     */
    public function subscriptions() : ?Subscriptions
    {
        return $this->subscriptions;
    }

    /**
     * Dispatch an event and call any handlers listening for it.
     *
     * @param Event $event
     */
    public function dispatch(Event $event) : void
    {
        $class = get_class($event);

        if (isset($this->subscriptions[$class])) {
            $handlers = $this->subscriptions[$class];

            foreach ($handlers as $handler) {
                $this->scheduler->defer(new HandleEvent($handler, $event, $this->logger));
            }
        }
    }
}
