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
     * The mapping of events to their listeners.
     *
     * @var \Rubix\Server\Services\Subscriptions
     */
    protected $subscriptions;

    /**
     * The job scheduler.
     *
     * @var \Rubix\Server\Services\Scheduler
     */
    protected $scheduler;

    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Rubix\Server\Services\Subscriptions $subscriptions
     * @param \Rubix\Server\Services\Scheduler $scheduler
     * @param \Psr\Log\LoggerInterface $logger
     * @param Subscriptions $subscriptions
     */
    public function __construct(Subscriptions $subscriptions, Scheduler $scheduler, LoggerInterface $logger)
    {
        $this->subscriptions = $subscriptions;
        $this->scheduler = $scheduler;
        $this->logger = $logger;
    }

    /**
     * Return the mapping of events to their listeners.
     *
     * @return \Rubix\Server\Services\Subscriptions
     */
    public function subscriptions() : Subscriptions
    {
        return $this->subscriptions;
    }

    /**
     * Dispatch an event and call any handlers listening for it.
     *
     * @param \Rubix\Server\Events\Event $event
     */
    public function dispatch(Event $event) : void
    {
        $class = get_class($event);

        if (isset($this->subscriptions[$class])) {
            $handlers = $this->subscriptions[$class];

            foreach ($handlers as $handler) {
                $job = new HandleEvent($handler, $event, $this->logger);

                $this->scheduler->defer($job);
            }
        }
    }
}
