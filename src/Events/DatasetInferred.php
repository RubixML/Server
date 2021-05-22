<?php

namespace Rubix\Server\Events;

use Rubix\ML\Datasets\Dataset;

/**
 * Dataset Inferred
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class DatasetInferred implements Event
{
    /**
     * The dataset that was used to make the predictions.
     *
     * @var \Rubix\ML\Datasets\Dataset
     */
    protected \Rubix\ML\Datasets\Dataset $dataset;

    /**
     * @param \Rubix\ML\Datasets\Dataset $dataset
     */
    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
     * Return the dataset object.
     *
     * @return \Rubix\ML\Datasets\Dataset
     */
    public function dataset() : Dataset
    {
        return $this->dataset;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Dataset Inferred';
    }
}
