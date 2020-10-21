<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Probabilistic;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Responses\ProbaResponse;

class ProbaHandler
{
    /**
     * The probabilistic model that is being served.
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
     * @param \Rubix\Server\Commands\Proba $command
     * @return \Rubix\Server\Responses\ProbaResponse
     */
    public function __invoke(Proba $command) : ProbaResponse
    {
        $probabilities = $this->estimator->proba($command->dataset());

        return new ProbaResponse($probabilities);
    }
}
