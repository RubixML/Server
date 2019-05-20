<?php

namespace Rubix\Server\Responses;

/**
 * Rank Response
 *
 * Return the anaomaly scores from a Rank command.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class RankResponse extends Response
{
    /**
     * The anomaly scores returned from the model.
     *
     * @var array
     */
    protected $scores;

    /**
     * Build the response from an associative array of data.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['scores'] ?? []);
    }

    /**
     * @param array $scores
     */
    public function __construct(array $scores)
    {
        $this->scores = $scores;
    }

    /**
     * Return the anomaly scores.
     *
     * @return array
     */
    public function scores() : array
    {
        return $this->scores;
    }

    /**
     * Return the message as an array.
     *
     * @return array
     */
    public function asArray() : array
    {
        return [
            'scores' => $this->scores,
        ];
    }
}
