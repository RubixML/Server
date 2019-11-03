<?php

namespace Rubix\Server;

use Psr\Log\LoggerAwareInterface;

interface Verbose extends LoggerAwareInterface
{
    /**
     * Return the number of requests that have been received.
     *
     * @var int
     */
    public function requests() : int;

    /**
     * Return the uptime of the server in seconds.
     *
     * @return int
     */
    public function uptime() : int;
}
