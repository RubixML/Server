<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\AnomalyDetectors\Ranking;
use Rubix\Server\Commands\Rank;
use Rubix\Server\Responses\RankResponse;

class RankHandler implements Handler
{
    /**
     * The ranking model that is being served.
     *
     * @var \Rubix\ML\AnomalyDetectors\Ranking
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\AnomalyDetectors\Ranking $estimator
     */
    public function __construct(Ranking $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Commands\Rank $command
     * @return \Rubix\Server\Responses\RankResponse
     */
    public function handle(Rank $command) : RankResponse
    {
        $dataset = Unlabeled::build($command->samples());

        $scores = $this->estimator->rank($dataset);

        return new RankResponse($scores);
    }
}
