<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Learner;
use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Payloads\PredictSamplePayload;

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
     * @return \Rubix\Server\Payloads\PredictSamplePayload
     */
    public function __invoke(PredictSample $command) : PredictSamplePayload
    {
        $prediction = $this->estimator->predictSample($command->sample());

        return new PredictSamplePayload($prediction);
    }
}
