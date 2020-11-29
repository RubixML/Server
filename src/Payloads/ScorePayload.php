<?php

namespace Rubix\Server\Payloads;

/**
 * Score Payload
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ScorePayload extends Payload
{
    /**
     * The anomaly scores returned from the model.
     *
     * @var float[]
     */
    protected $scores;

    /**
     * @param mixed[] $scores
     */
    public function __construct(array $scores)
    {
        $this->scores = $scores;
    }

    /**
     * Return the anomaly scores.
     *
     * @return float[]
     */
    public function scores() : array
    {
        return $this->scores;
    }

    /**
     * Return the message as an array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'data' => $this->scores,
        ];
    }
}
