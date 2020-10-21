<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Learner;
use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Responses\PredictSampleResponse;

class PredictSampleHandler
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
    public function __invoke(PredictSample $command) : PredictSampleResponse
    {
        $prediction = $this->estimator->predictSample($command->sample());

        return new PredictSampleResponse($prediction);
    }
}
