<?php

namespace Rubix\Server\Responses;

/**
 * Rank Sample Response
 *
 * This is the response returned from a rank sample command containing
 * the anomaly score returned from the model.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RankSampleResponse extends Response
{
    /**
     * The anomaly score returned from the model.
     *
     * @var mixed
     */
    protected $score;

    /**
     * Build the response from an associative array of data.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['prediction'] ?? []);
    }

    /**
     * @param mixed $score
     */
    public function __construct($score)
    {
        $this->score = $score;
    }

    /**
     * Return the anomaly score.
     *
     * @return mixed
     */
    public function score()
    {
        return $this->score;
    }

    /**
     * Return the message as an array.
     *
     * @return array
     */
    public function asArray() : array
    {
        return [
            'score' => $this->score,
        ];
    }
}
