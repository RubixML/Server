<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Events\ShuttingDown;
use React\Socket\ServerInterface as Socket;

class CloseSocket implements Listener
{
    /**
     * The open socket connection.
     *
     * @var \React\Socket\ServerInterface
     */
    protected $socket;

    /**
     * @param \React\Socket\ServerInterface $socket
     */
    public function __construct(Socket $socket)
    {
        $this->socket = $socket;
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
     * Close the open socket connection.
     *
     * @param \Rubix\Server\Events\ShuttingDown $event
     */
    public function __invoke(ShuttingDown $event) : void
    {
        $this->socket->close();
    }
}
