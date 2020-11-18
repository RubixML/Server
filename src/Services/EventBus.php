<?php

namespace Rubix\Server\Services;

use Rubix\Server\Services\EventMapping;
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
     * @var \Rubix\Server\Services\EventMapping
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
     * @param \Rubix\Server\Services\EventMapping $mapping
     * @param \React\EventLoop\LoopInterface $loop
     * @param \Psr\Log\LoggerInterface|null $logger
     * @param EventMapping $mapping
     */
    public function __construct(EventMapping $mapping, LoopInterface $loop, ?LoggerInterface $logger = null)
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
                $this->loop->futureTick(function () use ($event, $handler) {
                    $handler($event);
                });
            }
        }
    }

    /**
     * Log and rethrow exception.
     *
     * @param \Exception $exception
     * @throws \Exception
     */
    public function logError(Exception $exception) : void
    {
        if ($this->logger) {
            $this->logger->error((string) $exception);
        }

        throw $exception;
    }
}
