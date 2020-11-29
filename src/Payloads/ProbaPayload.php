<?php

namespace Rubix\Server\Payloads;

/**
 * Proba Payload
 *
 * This is the response from a Proba command containing the
 * probabilities obtained from the model.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ProbaPayload extends Payload
{
    /**
     * The probabilities returned from the model.
     *
     * @var array[]
     */
    protected $probabilities;

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
            'data' => $this->probabilities,
        ];
    }
}
