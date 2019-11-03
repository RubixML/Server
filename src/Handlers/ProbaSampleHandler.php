<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Probabilistic;
use Rubix\Server\Commands\ProbaSample;
use Rubix\Server\Responses\ProbaSampleResponse;

class ProbaSampleHandler implements Handler
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
     * @return \Rubix\Server\Responses\ProbaSampleResponse
     */
    public function handle(ProbaSample $command) : ProbaSampleResponse
    {
        return new ProbaSampleResponse($this->estimator->probaSample($command->sample()));
    }
}
