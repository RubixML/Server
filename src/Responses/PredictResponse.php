<?php

namespace Rubix\Server\Responses;

/**
 * Predict Response
 *
 * This is the response returned from a predict command containing the
 * predictions returned from the model.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class PredictResponse extends Response
{
    /**
     * The preditions returned from the model.
     *
     * @var array
     */
    protected $predictions;

    /**
     * Build the response from an associative array of data.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['predictions'] ?? []);
    }

    /**
     * @param array $predictions
     */
    public function __construct(array $predictions)
    {
        $this->predictions = $predictions;
    }

    /**
     * Return the predictions.
     *
     * @return array
     */
    public function predictions() : array
    {
        return $this->predictions;
    }

    /**
     * Return the message as an array.
     *
     * @return array
     */
    public function asArray() : array
    {
        return [
            'predictions' => $this->predictions,
        ];
    }
}
