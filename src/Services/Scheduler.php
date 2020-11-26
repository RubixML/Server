<?php

namespace Rubix\Server\Services;

use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;

class Scheduler
{
    /**
     * The event loop.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * @param \React\EventLoop\LoopInterface $loop
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
     * @return \React\EventLoop\TimerInterface
     */
    public function repeat(float $interval, callable $job) : TimerInterface
    {
        return $this->loop->addPeriodicTimer($interval, $job);
    }

    /**
     * Stop a timer from running.
     *
     * @param \React\EventLoop\TimerInterface $timer
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
