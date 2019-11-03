<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Ranking;
use Rubix\Server\Commands\RankSample;
use Rubix\Server\Responses\RankSampleResponse;

class RankSampleHandler implements Handler
{
    /**
     * The model that is being served.
     *
     * @var \Rubix\ML\Ranking
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\Ranking $estimator
     */
    public function __construct(Ranking $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Commands\RankSample $command
     * @return \Rubix\Server\Responses\RankSampleResponse
     */
    public function handle(RankSample $command) : RankSampleResponse
    {
        return new RankSampleResponse($this->estimator->rankSample($command->sample()));
    }
}
