<?php

namespace Rubix\Server\Commands;

use InvalidArgumentException;

class Proba implements Command
{
    /**
     * The name of the model.
     * 
     * @var string
     */
    protected $name;

    /**
     * The samples to predict.
     * 
     * @var array[]
     */
    protected $samples;

    /**
     * @param  string  $name
     * @param  array  $samples
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(string $name, array $samples) 
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Model name cannot be'
                . ' empty.');
        }

        $this->name = $name;
        $this->samples = $samples;
    }

    /**
     * Return the payload.
     * 
     * @return array
     */
    public function payload() : array
    {
        return [
            'name' => $this->name,
            'samples' => $this->samples,
        ];
    }
}