<?php

namespace Rubix\Server\HTTP\Requests;

use Rubix\ML\Datasets\Dataset;

class ScoreRequest extends JSONRequest
{
    /**
     * @param \Rubix\ML\Datasets\Dataset $dataset
     */
    public function __construct(Dataset $dataset)
    {
        parent::__construct('POST', '/model/anomaly-scores', [
            'samples' => $dataset->samples(),
        ]);
    }
}
