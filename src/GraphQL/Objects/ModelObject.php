<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\Model;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\GraphQL\Enums\EstimatorTypeEnum;
use Rubix\Server\GraphQL\Enums\DataTypeEnum;
use Rubix\Server\GraphQL\InputObjects\DatasetInputObject;
use Rubix\Server\GraphQL\Scalars\PredictionScalar;
use Rubix\Server\GraphQL\Scalars\LongIntegerScalar;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\UserError;
use Generator;

class ModelObject extends ObjectType
{
    /**
     * The singleton instance of the object type.
     *
     * @var self|null
     */
    protected static $instance;

    /**
     * @return self
     */
    public static function singleton() : self
    {
        return self::$instance ?? self::$instance = new self([
            'name' => 'Model',
            'description' => 'The model.',
            'fields' => [
                'type' => [
                    'type' => Type::nonNull(EstimatorTypeEnum::singleton()),
                    'resolve' => function (Model $model) : int {
                        return $model->type();
                    },
                ],
                'compatibility' => [
                    'type' => Type::nonNull(Type::listOf(DataTypeEnum::singleton())),
                    'resolve' => function (Model $model) : array {
                        return $model->compatibility();
                    },
                ],
                'hyperparameters' => [
                    'type' => Type::nonNull(Type::listOf(HyperparameterObject::singleton())),
                    'resolve' => function (Model $model) : Generator {
                        foreach ($model->hyperparameters() as $name => $value) {
                            yield [
                                'name' => $name,
                                'value' => $value,
                            ];
                        }
                    },
                ],
                'interfaces' => [
                    'type' => Type::nonNull(EstimatorInterfacesObject::singleton()),
                    'resolve' => function (Model $model) : Model {
                        return $model;
                    },
                ],
                'predictions' => [
                    'description' => 'Return the predictions on a dataset.',
                    'type' => Type::listOf(PredictionScalar::singleton()),
                    'args' => [
                        'dataset' => Type::nonNull(DatasetInputObject::singleton()),
                    ],
                    'resolve' => function (Model $model, array $args) : array {
                        return $model->predict(new Unlabeled($args['dataset']['samples']));
                    },
                ],
                'probabilities' => [
                    'description' => 'Predict the joint probabilities of a dataset.',
                    'type' => Type::listOf(Type::listOf(ProbabilityObject::singleton())),
                    'args' => [
                        'dataset' => Type::nonNull(DatasetInputObject::singleton()),
                    ],
                    'resolve' => function (Model $model, array $args) : Generator {
                        if (!$model->isProbabilistic()) {
                            throw new UserError('Estimator must implement the Probabilistic interface.');
                        }

                        $probabilities = $model->proba(new Unlabeled($args['dataset']['samples']));

                        foreach ($probabilities as $dist) {
                            foreach ($dist as $class => &$probability) {
                                $probability = [
                                    'class' => $class,
                                    'value' => $probability,
                                ];
                            }

                            yield $dist;
                        }
                    },
                ],
                'scores' => [
                    'description' => 'Return the anomaly scores of the samples in a dataset.',
                    'type' => Type::listOf(Type::float()),
                    'args' => [
                        'dataset' => Type::nonNull(DatasetInputObject::singleton()),
                    ],
                    'resolve' => function (Model $model, array $args) : array {
                        if ($model->isScoring()) {
                            throw new UserError('Estimator must implement the Ranking interface.');
                        }

                        return $model->score(new Unlabeled($args['dataset']['samples']));
                    },
                ],
                'numSamplesInferred' => [
                    'description' => 'The number of samples that have been predicted by the model so far.',
                    'type' => Type::nonNull(LongIntegerScalar::singleton()),
                    'resolve' => function (Model $model) : int {
                        return $model->numSamplesInferred();
                    },
                ]
            ],
        ]);
    }
}
