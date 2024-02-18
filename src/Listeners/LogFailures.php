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
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
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
     * @param Failure $event
     */
    public function __invoke(Failure $event) : void
    {
        $this->logger->error((string) $event->exception());
    }
}
