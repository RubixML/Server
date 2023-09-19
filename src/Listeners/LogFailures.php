<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Events\RequestFailed;
use Rubix\Server\Events\Failure;
use Psr\Log\LoggerInterface;

class LogFailures implements Listener
{
    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected \Psr\Log\LoggerInterface $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Return the events that this listener subscribes to.
     *
     * @return array<array<\Rubix\Server\Listeners\Listener>>
     */
    public function events() : array
    {
        return [
            RequestFailed::class => [$this],
        ];
    }

    /**
     * Log
     *
     * @param \Rubix\Server\Events\Failure $event
     */
    public function __invoke(Failure $event) : void
    {
        $this->logger->error((string) $event->exception());
    }
}
