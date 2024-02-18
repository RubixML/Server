<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Events\ShuttingDown;
use Rubix\Server\Services\Scheduler;

class StopTimers implements Listener
{
    /**
     * The job scheduler.
     *
     * @var Scheduler
     */
    protected Scheduler $scheduler;

    /**
     * The timers.
     *x
     * @var \React\EventLoop\TimerInterface[]
     */
    protected $timers;

    /**
     * @param Scheduler $scheduler
     * @param \React\EventLoop\TimerInterface[] $timers
     */
    public function __construct(Scheduler $scheduler, array $timers)
    {
        $this->scheduler = $scheduler;
        $this->timers = $timers;
    }

    /**
     * Return the events that this listener subscribes to.
     *
     * @return array<array<\Rubix\Server\Listeners\Listener>>
     */
    public function events() : array
    {
        return [
            ShuttingDown::class => [$this],
        ];
    }

    /**
     * Close the open SSE timers.
     *
     * @param ShuttingDown $event
     */
    public function __invoke(ShuttingDown $event) : void
    {
        foreach ($this->timers as $timer) {
            $this->scheduler->stop($timer);
        }
    }
}
