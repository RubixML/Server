<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Estimator;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Responses\PredictResponse;

class PredictHandler implements Handler
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
     * @return \Rubix\Server\Responses\PredictResponse
     */
    public function handle(Predict $command) : PredictResponse
    {
        $dataset = Unlabeled::build($command->samples());

        $predictions = $this->estimator->predict($dataset);

        return new PredictResponse($predictions);
    }
}
