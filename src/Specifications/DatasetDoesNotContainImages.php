<?php

namespace Rubix\Server\Specifications;

use Rubix\ML\DataType;
use Rubix\ML\Datasets\Dataset;
use Rubix\Server\Exceptions\ValidationException;

use function in_array;

/**
 * @internal
 */
class DatasetDoesNotContainImages extends Specification
{
    /**
     * The dataset under validation.
     *
     * @var \Rubix\ML\Datasets\Dataset
     */
    protected $dataset;

    /**
     * Build a specification object with the given arguments.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return self
     */
    public static function with(Dataset $dataset) : self
    {
        return new self($dataset);
    }

    /**
     * @param \Rubix\ML\Datasets\Dataset $dataset
     */
    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
     * Perform a check of the specification and throw an exception if invalid.
     *
     * @throws \Rubix\Server\Exceptions\ValidationException
     */
    public function check() : void
    {
        if (in_array(DataType::image(), $this->dataset->columnTypes())) {
            throw new ValidationException('Dataset must not contain images.');
        }
    }
}
