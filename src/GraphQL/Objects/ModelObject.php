<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Models\Model;
use Rubix\Server\GraphQL\Enums\EstimatorTypeEnum;
use Rubix\Server\GraphQL\Enums\DataTypeEnum;
use Rubix\Server\GraphQL\Scalars\FeatureScalar;
use Rubix\Server\GraphQL\Scalars\PredictionScalar;
use GraphQL\Type\Definition\Type;

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
                    'type' => Type::listOf(DataTypeEnum::singleton()),
                    'resolve' => function (Model $model) : array {
                        return $model->compatibility();
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
                        'dataset' => Type::listOf(Type::listOf(FeatureScalar::singleton())),
                    ],
                    'resolve' => function (Model $model, array $args) : array {
                        return $model->predict(new Unlabeled($args['dataset']));
                    },
                ],
                'numSamplesInferred' => [
                    'description' => 'The number of samples that have been predicted by the model so far.',
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (Model $model) : int {
                        return $model->numSamplesInferred();
                    },
                ]
            ],
        ]);
    }
}
