<?php

namespace Rubix\Server\Commands;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;

/**
 * Predict
 *
 * Return the predictions of the samples provided in a dataset from the
 * model running on the server.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class Predict extends Command
{
    /**
     * The dataset to predict.
     *
     * @var \Rubix\ML\Datasets\Dataset
     */
    protected $dataset;

    /**
     * Build the command from an associative array of data.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self(new Unlabeled($data['samples'] ?? []));
    }

    /**
     * @param \Rubix\ML\Datasets\Dataset $dataset
     */
    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
     * Return the dataset to predict.
     *
     * @return \Rubix\ML\Datasets\Dataset
     */
    public function dataset() : Dataset
    {
        return $this->dataset;
    }

    /**
     * Return the message as an array.
     *
     * @return array
     */
    public function asArray() : array
    {
        return [
            'samples' => $this->dataset->samples(),
        ];
    }
}
