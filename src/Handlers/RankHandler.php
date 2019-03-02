<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Ranking;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Rank;
use Rubix\Server\Responses\RankResponse;

class RankHandler implements Handler
{
    /**
     * The ranking model that is being served.
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
