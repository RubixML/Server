<?php

namespace Rubix\Server\Responses;

/**
 * Score Response
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ScoreResponse extends Response
{
    /**
     * The anomaly scores returned from the model.
     *
     * @var float[]
     */
    protected $scores;

    /**
     * Build the response from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['scores'] ?? []);
    }

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
            'scores' => $this->scores,
        ];
    }
}
