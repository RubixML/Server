<?php

namespace Rubix\Server\Jobs;

use Rubix\Server\Events\Event;
use Psr\Log\LoggerInterface;
use Exception;

use function call_user_func;

class HandleEvent implements Job
{
    /**
     * The handler to be called.
     *
     * @var callable
     */
    protected $handler;

    /**
     * The event to be handled.
     *
     * @var \Rubix\Server\Events\Event
     */
    protected $event;

    /**
     * A logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param callable $handler
     * @param \Rubix\Server\Events\Event $event
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(callable $handler, Event $event, LoggerInterface $logger)
    {
        $this->handler = $handler;
        $this->event = $event;
        $this->logger = $logger;
    }

    /**
     * Run the job.
     */
    public function __invoke() : void
    {
        try {
            call_user_func($this->handler, $this->event);
        } catch (Exception $exception) {
            $this->logger->error((string) $exception);
        }
    }
}
