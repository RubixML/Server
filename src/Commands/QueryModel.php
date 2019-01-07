<?php

namespace Rubix\Server\Commands;

use InvalidArgumentException;

class QueryModel implements Command
{
    /**
     * The name of the model.
     * 
     * @var string
     */
    protected $name;

    /**
     * @param  string  $name
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(string $name) 
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Model name cannot be'
                . ' empty.');
        }

        $this->name = $name;
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
        ];
    }
}