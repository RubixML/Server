<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;

interface Server
{
    /**
     * Boot up the server.
     *
     * @param \Rubix\ML\Estimator $estimator
     */
    public function serve(Estimator $estimator) : void;
}
