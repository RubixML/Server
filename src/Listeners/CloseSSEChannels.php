<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Events\ShuttingDown;

class CloseSSEChannels implements Listener
{
    /**
     * The open SSE channels.
     *
     * @var \Rubix\Server\Services\SSEChannel[]
     */
    protected array $channels;

    /**
     * @param \Rubix\Server\Services\SSEChannel[] $channels
     */
    public function __construct(array $channels)
    {
        $this->channels = $channels;
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
     * Close the open SSE channels.
     *
     * @param ShuttingDown $event
     */
    public function __invoke(ShuttingDown $event) : void
    {
        foreach ($this->channels as $channel) {
            $channel->close();
        }
    }
}
