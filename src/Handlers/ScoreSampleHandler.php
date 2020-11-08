<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Ranking;
use Rubix\Server\Commands\ScoreSample;
use Rubix\Server\Payloads\ScoreSamplePayload;

class ScoreSampleHandler
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
     * @param \Rubix\Server\Commands\ScoreSample $command
     * @return \Rubix\Server\Payloads\ScoreSamplePayload
     */
    public function __invoke(ScoreSample $command) : ScoreSamplePayload
    {
        $score = $this->estimator->scoreSample($command->sample());

        return new ScoreSamplePayload($score);
    }
}
