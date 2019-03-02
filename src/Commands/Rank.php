<?php

namespace Rubix\Server\Commands;

/**
 * Rank
 *
 * Apply an arbitrary unnormalized scoring function over the the samples.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class Rank extends Command
{
    /**
     * The samples to predict.
     *
     * @var array[]
     */
    protected $samples;

    /**
     * Build the command from an associative array of data.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['samples'] ?? []);
    }

    /**
     * @param array $samples
     * @throws \InvalidArgumentException
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
     * Return the message as an array.
     *
     * @return array
     */
    public function asArray() : array
    {
        return [
            'samples' => $this->samples,
        ];
    }
}
