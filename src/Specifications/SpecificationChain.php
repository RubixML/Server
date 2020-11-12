<?php

namespace Rubix\Server\Specifications;

class SpecificationChain extends Specification
{
    /**
     * A list of specifications to check in order.
     *
     * @var iterable<\Rubix\Server\Specifications\Specification>
     */
    protected $specifications;

    /**
     * Build a specification object with the given arguments.
     *
     * @param iterable<\Rubix\Server\Specifications\Specification> $specifications
     * @return self
     */
    public static function with(iterable $specifications) : self
    {
        return new self($specifications);
    }

    /**
     * @param iterable<\Rubix\Server\Specifications\Specification> $specifications
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(iterable $specifications)
    {
        $this->specifications = $specifications;
    }

    /**
     * Perform a check of the specification and throw an exception if invalid.
     */
    public function check() : void
    {
        foreach ($this->specifications as $specification) {
            $specification->check();
        }
    }
}
