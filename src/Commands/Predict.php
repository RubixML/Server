<?php

namespace Rubix\Server\Commands;

use InvalidArgumentException;

/**
 * Predict
 *
 * Make predictions on unknown samples and return them in an array.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class Predict implements Command
{
    /**
     * The samples to predict.
     * 
     * @var array[]
     */
    protected $samples;

    /**
     * @param  array  $samples
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(array $samples) 
    {
        $this->samples = $samples;
    }

    /**
     * Return the samples to rpedict.
     * 
     * @return array
     */
    public function samples() : array
    {
        return $this->samples;
    }

    /**
     * Return the payload.
     * 
     * @return array
     */
    public function payload() : array
    {
        return [
            'samples' => $this->samples,
        ];
    }
}