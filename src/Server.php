<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Psr\Log\LoggerAwareInterface;

interface Server extends LoggerAwareInterface
{
    /**
     * Serve a model.
     *
     * @param \Rubix\ML\Estimator $estimator
     */
    public function serve(Estimator $estimator) : void;

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
