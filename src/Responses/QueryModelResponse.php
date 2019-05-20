<?php

namespace Rubix\Server\Responses;

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
     * @var string
     */
    protected $type;

    /**
     * The data types that the model is compatible with.
     *
     * @var int[]
     */
    protected $compatibility;

    /**
     * Is the model probabilistic?
     *
     * @var bool
     */
    protected $probabilistic;

    /**
     * Does the model score unknown samples?
     *
     * @var bool
     */
    protected $ranking;

    /**
     * Build the response from an associative array of data.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        $type = $data['type'] ?? 'unknown';
        $compatibility = $data['compatibility'] ?? [];
        $probabilistic = $data['probabilistic'] ?? false;
        $ranking = $data['ranking'] ?? false;

        return new self($type, $compatibility, $probabilistic, $ranking);
    }

    /**
     * @param string $type
     * @param array $compatibility
     * @param bool $probabilistic
     * @param bool $ranking
     */
    public function __construct(string $type, array $compatibility, bool $probabilistic, bool $ranking)
    {
        $this->type = $type;
        $this->compatibility = $compatibility;
        $this->probabilistic = $probabilistic;
        $this->ranking = $ranking;
    }

    /**
     * Return the model type.
     *
     * @return string
     */
    public function type() : string
    {
        return $this->type;
    }

    /**
     * Return the data types the model is compatible with.
     *
     * @return int[]
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
     * @return array
     */
    public function asArray() : array
    {
        return [
            'type' => $this->type,
            'compatibility' => $this->compatibility,
            'probabilistic' => $this->probabilistic,
            'ranking' => $this->ranking,
        ];
    }
}
