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
}
