<?php

namespace Rubix\Server\Responses;

/**
 * Proba Response
 *
 * This is the response from a Proba command containing the
 * probabilities obtained from the model.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ProbaResponse extends Response
{
    /**
     * The probabilities returned from the model.
     *
     * @var array[]
     */
    protected $probabilities;

    /**
     * Build the response from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['probabilities'] ?? []);
    }

    /**
     * @param mixed[] $probabilities
     */
    public function __construct(array $probabilities)
    {
        $this->probabilities = $probabilities;
    }

    /**
     * Return the probabilities.
     *
     * @return array[]
     */
    public function probabilities() : array
    {
        return $this->probabilities;
    }

    /**
     * Return the message as an array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'probabilities' => $this->probabilities,
        ];
    }
}
