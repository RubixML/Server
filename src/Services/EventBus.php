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
    protected $mapping;

    /**
     * The event loop.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * @param \Rubix\Server\Services\Subscriptions $mapping
     * @param \React\EventLoop\LoopInterface $loop
     * @param \Psr\Log\LoggerInterface|null $logger
     * @param Subscriptions $mapping
     */
    public function __construct(Subscriptions $mapping, LoopInterface $loop, ?LoggerInterface $logger = null)
    {
        $this->mapping = $mapping;
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

        if (isset($this->mapping[$class])) {
            $handlers = $this->mapping[$class];

            foreach ($handlers as $handler) {
                $job = function () use ($event, $handler) {
                    try {
                        $handler($event);
                    } catch (Exception $exception) {
                        if ($this->logger) {
                            $this->logger->error((string) $exception);
                        }
                    }
                };

                $this->loop->futureTick($job);
            }
        }
    }
}
