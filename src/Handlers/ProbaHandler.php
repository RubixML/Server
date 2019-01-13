<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Probabilistic;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Proba;

class ProbaHandler implements Handler
{
    /**
     * The probabilistic model that is being served.
     * 
     * @var \Rubix\ML\Probabilistic
     */
    protected $estimator;

    /**
     * @param  \Rubix\ML\Probabilistic  $estimator
     * @return void
     */
    public function __construct(Probabilistic $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     * 
     * @param  \Rubix\Server\Commands\Proba  $command
     * @return array
     */
    public function handle(Proba $command) : array
    {        
        $dataset = Unlabeled::build($command->samples());

        $probabilities = $this->estimator->proba($dataset);

        return $probabilities;
    }
}