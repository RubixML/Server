<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Proba;
use InvalidArgumentException;
use RuntimeException;

class ProbaHandler implements Handler
{
    /**
     * The mapping of model names to their estimator instance.
     * 
     * @var \Rubix\ML\Estimator[]
     */
    protected $models;

    /**
     * @param  array  $models
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(array $models)
    {
        foreach ($models as $name => $estimator) {
            if (!is_string($name) or empty($name)) {
                throw new InvalidArgumentException('Model name must be'
                    . ' a non empty string.');
            }

            if (!$estimator instanceof $estimator) {
                throw new InvalidArgumentException('Model must implement'
                    . ' the estimator interface.');
            }
        }

        $this->models = $models;
    }

    /**
     * Handle the command.
     * 
     * @param  \Rubix\Server\Commands\Proba  $command
     * @throws \RuntimeException
     * @return array
     */
    public function handle(Proba $command) : array
    {
        $payload = $command->payload();

        $name = $payload['name'];
        
        if (!isset($this->models[$name])) {
            throw new RuntimeException("Model named '$name'"
                . ' does not exist.');
        }
        
        $estimator = $this->models[$name];

        if (!$estimator instanceof Probabilistic) {
            throw new RuntimeException('Estimator must implment'
                . ' the probabilistic interface.');
        }
        
        $dataset = Unlabeled::build($payload['samples']);

        $probabilities = $estimator->proba($dataset);

        return [
            'probabilities' => $probabilities,
        ];
    }
}