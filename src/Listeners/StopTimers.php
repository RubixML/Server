<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Events\ShuttingDown;
use React\EventLoop\LoopInterface;

class StopTimers implements Listener
{
    /**
     * The event loop.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * The timers.
     *
     * @var \React\EventLoop\TimerInterface[]
     */
    protected $timers;

    /**
     * @param \React\EventLoop\LoopInterface $loop
     * @param \React\EventLoop\TimerInterface[] $timers
     */
    public function __construct(LoopInterface $loop, array $timers)
    {
        $this->loop = $loop;
        $this->timers = $timers;
    }

    /**
     * Return the events that this listener subscribes to.
     *
     * @return array[]
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
     * @param \Rubix\Server\Events\ShuttingDown $event
     */
    public function __invoke(ShuttingDown $event) : void
    {
        foreach ($this->timers as $timer) {
            $this->loop->cancelTimer($timer);
        }
    }
}
