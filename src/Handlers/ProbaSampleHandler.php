<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Probabilistic;
use Rubix\Server\Commands\ProbaSample;
use Rubix\Server\Payloads\ProbaSamplePayload;

class ProbaSampleHandler
{
    /**
     * The model that is being served.
     *
     * @var \Rubix\ML\Probabilistic
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\Probabilistic $estimator
     */
    public function __construct(Probabilistic $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Commands\ProbaSample $command
     * @return \Rubix\Server\Payloads\ProbaSamplePayload
     */
    public function __invoke(ProbaSample $command) : ProbaSamplePayload
    {
        $probabilities = $this->estimator->probaSample($command->sample());

        return new ProbaSamplePayload($probabilities);
    }
}
