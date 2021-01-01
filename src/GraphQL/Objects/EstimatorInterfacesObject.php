<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\Model;
use GraphQL\Type\Definition\Type;

class EstimatorInterfacesObject extends ObjectType
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
            'name' => 'EstimatorInterfaces',
            'description' => 'The interfaces to the underlying estimator.',
            'fields' => [
                'probabilistic' => [
                    'description' => 'Does the estimator implement the Probabilistic interface?',
                    'type' => Type::nonNull(Type::boolean()),
                    'resolve' => function (Model $model) : bool {
                        return $model->isProbabilistic();
                    },
                ],
                'scoring' => [
                    'description' => 'Does the estimator implement the Scoring interface?',
                    'type' => Type::nonNull(Type::boolean()),
                    'resolve' => function (Model $model) : bool {
                        return $model->isScoring();
                    },
                ],
            ],
        ]);
    }
}
