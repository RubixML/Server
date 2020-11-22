<?php

namespace Rubix\Server\Listeners;

interface Listener
{
    /**
     * Return the events that this listener subscribes to and their handlers.
     *
     * @return array[]
     */
    public function events() : array;
}
