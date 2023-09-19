<?php

namespace Rubix\Server\Listeners;

interface Listener
{
    /**
     * Return the events that this listener subscribes to and their handlers.
     *
     * @return array<array<\Rubix\Server\Listeners\Listener|callable>>
     */
    public function events() : array;
}
