<?php

namespace Rubix\Server\Services;

use Rubix\Server\Events\Event;
use React\EventLoop\LoopInterface;
use Psr\Log\LoggerInterface;
use Exception;

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
     * The event loop.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Rubix\Server\Services\Subscriptions $subscriptions
     * @param \React\EventLoop\LoopInterface $loop
     * @param \Psr\Log\LoggerInterface $logger
     * @param Subscriptions $subscriptions
     */
    public function __construct(Subscriptions $subscriptions, LoopInterface $loop, LoggerInterface $logger)
    {
        $this->subscriptions = $subscriptions;
        $this->loop = $loop;
        $this->logger = $logger;
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
                $job = function () use ($event, $handler) {
                    try {
                        $handler($event);
                    } catch (Exception $exception) {
                        $this->logger->error((string) $exception);
                    }
                };

                $this->loop->futureTick($job);
            }
        }
    }
}
