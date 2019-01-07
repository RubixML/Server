<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Estimator;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Predict;
use InvalidArgumentException;

class PredictHandler
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
     * @param  \Rubix\Server\Commands\Predict  $command
     * @return array
     */
    public function handle(Predict $command) : array
    {
        $payload = $command->payload();
        
        $estimator = $this->models[$payload['name']];
        
        $dataset = Unlabeled::build($payload['samples']);

        $predictions = $estimator->predict($dataset);

        return [
            'predictions' => $predictions,
        ];
    }
}