<?php

namespace Rubix\Server\Responses;

use Rubix\ML\DataType;
use Rubix\ML\EstimatorType;

/**
 * Query Model Response
 *
 * This response contains the properties of the underlying estimator
 * instance being served such as type and compatibility.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class QueryModelResponse extends Response
{
    /**
     * The model type.
     *
     * @var \Rubix\ML\EstimatorType
     */
    protected $type;

    /**
     * The data types that the model is compatible with.
     *
     * @var \Rubix\ML\DataType[]
     */
    protected $compatibility;

    /**
     * Is the model probabilistic?
     *
     * @var bool
     */
    protected $probabilistic;

    /**
     * Is the model ranking?
     *
     * @var bool
     */
    protected $ranking;

    /**
     * Build the response from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        $type = new EstimatorType($data['type']);

        $compatibility = array_map(function ($code) {
            return new DataType($code);
        }, $data['compatibility']);

        $probabilistic = $data['probabilistic'];
        $ranking = $data['ranking'];

        return new self($type, $compatibility, $probabilistic, $ranking);
    }

    /**
     * @param \Rubix\ML\EstimatorType $type
     * @param \Rubix\ML\DataType[] $compatibility
     * @param bool $probabilistic
     * @param bool $ranking
     */
    public function __construct(EstimatorType $type, array $compatibility, bool $probabilistic, bool $ranking)
    {
        $this->type = $type;
        $this->compatibility = $compatibility;
        $this->probabilistic = $probabilistic;
        $this->ranking = $ranking;
    }

    /**
     * Return the model type.
     *
     * @return \Rubix\ML\EstimatorType
     */
    public function type() : EstimatorType
    {
        return $this->type;
    }

    /**
     * Return the data types the model is compatible with.
     *
     * @return \Rubix\ML\DataType[]
     */
    public function compatibility() : array
    {
        return $this->compatibility;
    }

    /**
     * Is the model probabilistic?
     *
     * @return bool
     */
    public function probabilistic() : bool
    {
        return $this->probabilistic;
    }

    /**
     * Is the model ranking?
     *
     * @return bool
     */
    public function ranking() : bool
    {
        return $this->ranking;
    }

    /**
     * Return the message as an array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        $compatibility = array_map(function ($type) {
            return $type->code();
        }, $this->compatibility);

        return [
            'type' => $this->type->code(),
            'compatibility' => $compatibility,
            'probabilistic' => $this->probabilistic,
            'ranking' => $this->ranking,
        ];
    }
}
