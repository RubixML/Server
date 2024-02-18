<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Events\ShuttingDown;
use React\Socket\ServerInterface as Socket;

class CloseSocket implements Listener
{
    /**
     * The open socket connection.
     *
     * @var Socket
     */
    protected Socket $socket;

    /**
     * @param Socket $socket
     */
    public function __construct(Socket $socket)
    {
        $this->socket = $socket;
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
     * Close the open socket connection.
     *
     * @param ShuttingDown $event
     */
    public function __invoke(ShuttingDown $event) : void
    {
        $this->socket->close();
    }
}
