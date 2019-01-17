<?php

namespace Rubix\Server\Responses;

/**
 * Query Model Response
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
     * Is the model probabilistic?
     * 
     * @var bool
     */
    protected $probabilistic;

    /**
     * Build the message from an associative array of data.
     * 
     * @param  array  $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        $type = $data['type'] ?? 'unknown';
        $probabilistic = $data['probabilistic'] ?? false;

        return new self($type, $probabilistic);
    }

    /**
     * @param  string  $type
     * @param  bool   $probabilistic
     * @return void
     */
    public function __construct(string $type, bool $probabilistic) 
    {
        $this->type = $type;
        $this->probabilistic = $probabilistic;
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
     * Is the model probabilistic?
     * 
     * @return bool
     */
    public function probabilistic() : bool
    {
        return $this->probabilistic;
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
            'probabilistic' => $this->probabilistic,
        ];
    }
}