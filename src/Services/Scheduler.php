<?php

namespace Rubix\Server\Services;

use Rubix\Server\Jobs\Job;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;

class Scheduler
{
    /**
     * The event loop.
     *
     * @var LoopInterface
     */
    protected LoopInterface $loop;

    /**
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * Repeat at job every n seconds.
     *
     * @param float $interval
     * @param callable $job
     * @return TimerInterface
     */
    public function repeat(float $interval, callable $job) : TimerInterface
    {
        return $this->loop->addPeriodicTimer($interval, $job);
    }

    /**
     * Stop a timer from running.
     *
     * @param TimerInterface $timer
     */
    public function stop(TimerInterface $timer) : void
    {
        $this->loop->cancelTimer($timer);
    }

    /**
     * Defer a job until sometime in the future.
     *
     * @param callable $job
     */
    public function defer(callable $job) : void
    {
        $this->loop->futureTick($job);
    }
}
