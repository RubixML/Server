<?php

namespace Rubix\Server\GraphQL\Objects;

use GraphQL\Type\Definition\Type;

class HyperparameterObject extends ObjectType
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
            'name' => 'Hyper-parameter',
            'description' => 'A hyper-parameter object.',
            'fields' => [
                'name' => [
                    'description' => 'The name of the hyper-parameter.',
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => function (array $hyperparameter) : string {
                        return $hyperparameter['name'];
                    },
                ],
                'value' => [
                    'description' => 'The value of the hyper-parameter.',
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => function (array $hyperparameter) : string {
                        return $hyperparameter['value'];
                    },
                ],
            ],
        ]);
    }
}
