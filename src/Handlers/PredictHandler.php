<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Estimator;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Payloads\PredictPayload;

class PredictHandler
{
    /**
     * The model that is being served.
     *
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\Estimator $estimator
     */
    public function __construct(Estimator $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Commands\Predict $command
     * @return \Rubix\Server\Payloads\PredictPayload
     */
    public function __invoke(Predict $command) : PredictPayload
    {
        $predictions = $this->estimator->predict($command->dataset());

        return new PredictPayload($predictions);
    }
}
