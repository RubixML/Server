<?php

namespace Rubix\Server\Http\Requests;

use Rubix\ML\Datasets\Dataset;

class ProbaRequest extends JSONRequest
{
    /**
     * @param \Rubix\ML\Datasets\Dataset $dataset
     */
    public function __construct(Dataset $dataset)
    {
        parent::__construct('POST', '/model/probabilities', [
            'samples' => $dataset->samples(),
        ]);
    }
}
