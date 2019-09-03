<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Learner;
use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Responses\PredictSampleResponse;

class PredictSampleHandler implements Handler
{
    /**
     * The model that is being served.
     *
     * @var \Rubix\ML\Learner
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\Learner $estimator
     */
    public function __construct(Learner $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Commands\PredictSample $command
     * @return \Rubix\Server\Responses\PredictSampleResponse
     */
    public function handle(PredictSample $command) : PredictSampleResponse
    {
        return new PredictSampleResponse($this->estimator->predictSample($command->sample()));
    }
}
