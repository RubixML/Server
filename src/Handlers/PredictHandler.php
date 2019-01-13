<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Estimator;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Predict;

class PredictHandler implements Handler
{
    /**
     * The model that is being served.
     * 
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * @param  \Rubix\ML\Estimator  $estimator
     * @return void
     */
    public function __construct(Estimator $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     * 
     * @param  \Rubix\Server\Commands\Predict  $command
     * @return array
     */
    public function handle(Predict $command) : array
    {        
        $dataset = Unlabeled::build($command->samples());

        $predictions = $this->estimator->predict($dataset);

        return $predictions;
    }
}