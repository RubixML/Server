<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Events\QueryFailed;
use Rubix\Server\Events\Failure;
use Psr\Log\LoggerInterface;

class LogFailures implements Listener
{
    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
     * @return array[]
     */
    public function events() : array
    {
        return [
            QueryFailed::class => [$this],
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
