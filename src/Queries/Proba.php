<?php

namespace Rubix\Server\Queries;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Exceptions\ValidationException;

/**
 * Proba
 *
 * Return the probabilistic predictions from a probabilistic model.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class Proba extends Query
{
    /**
     * The dataset to predict.
     *
     * @var \Rubix\ML\Datasets\Dataset<array>
     */
    protected $dataset;

    /**
     * Build the query from an associative array of data.
     *
     * @param mixed[] $data
     * @throws \Rubix\Server\Exceptions\ValidationException
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        if (!isset($data['samples'])) {
            throw new ValidationException('Samples property must be present.');
        }

        return new self(new Unlabeled($data['samples']));
    }

    /**
     * @param \Rubix\ML\Datasets\Dataset<array> $dataset
     */
    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
     * Return the dataset to predict.
     *
     * @return \Rubix\ML\Datasets\Dataset<array>
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
        return 'Proba';
    }
}
