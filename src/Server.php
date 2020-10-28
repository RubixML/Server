<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;

interface Server
{
    /**
     * Serve a model.
     *
     * @param \Rubix\ML\Estimator $estimator
     */
    public function serve(Estimator $estimator) : void;
}
