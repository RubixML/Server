<?php

namespace Rubix\Server\Specifications;

use Rubix\ML\Learner;
use Rubix\Server\Exceptions\ValidationException;

/**
 * @internal
 */
class LearnerIsTrained extends Specification
{
    /**
     * The the learner.
     *
     * @var \Rubix\ML\Learner
     */
    protected $learner;

    /**
     * Build a specification object with the given arguments.
     *
     * @param \Rubix\ML\Learner $learner
     * @return self
     */
    public static function with(Learner $learner) : self
    {
        return new self($learner);
    }

    /**
     * @param \Rubix\ML\Learner $learner
     */
    public function __construct(Learner $learner)
    {
        $this->learner = $learner;
    }

    /**
     * Perform a check of the specification and throw an exception if invalid.
     *
     * @throws \Rubix\Server\Exceptions\ValidationException
     */
    public function check() : void
    {
        if (!$this->learner->trained()) {
            throw new ValidationException('Learner must be trained.');
        }
    }
}
