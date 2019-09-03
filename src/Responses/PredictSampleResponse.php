<?php

namespace Rubix\Server\Responses;

/**
 * Predict Sample Response
 *
 * This is the response returned from a predict sample command containing
 * the prediction returned from the model.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class PredictSampleResponse extends Response
{
    /**
     * The predition returned from the model.
     *
     * @var mixed
     */
    protected $prediction;

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
     * @param mixed $prediction
     */
    public function __construct($prediction)
    {
        $this->prediction = $prediction;
    }

    /**
     * Return the prediction.
     *
     * @return mixed
     */
    public function prediction()
    {
        return $this->prediction;
    }

    /**
     * Return the message as an array.
     *
     * @return array
     */
    public function asArray() : array
    {
        return [
            'prediction' => $this->prediction,
        ];
    }
}
