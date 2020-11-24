<?php

namespace Rubix\Server\Events;

use Rubix\Server\Server;

/**
 * Shutting Down
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ShuttingDown implements Event
{
    /**
     * The server instance.
     *
     * @var \Rubix\Server\Server
     */
    protected $server;

    /**
     * @param \Rubix\Server\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Return the server instance.
     *
     * @return \Rubix\Server\Server
     */
    public function server() : Server
    {
        return $this->server;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Shutting Down';
    }
}
