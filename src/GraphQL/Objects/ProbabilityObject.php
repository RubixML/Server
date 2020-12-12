<?php

namespace Rubix\Server\GraphQL\Objects;

use GraphQL\Type\Definition\Type;

class ProbabilityObject extends ObjectType
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
            'name' => 'Probability',
            'description' => 'A probability object.',
            'fields' => [
                'class' => [
                    'description' => 'The class label.',
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => function (array $probability) : string {
                        return $probability['class'];
                    },
                ],
                'value' => [
                    'description' => 'The probability value.',
                    'type' => Type::nonNull(Type::float()),
                    'resolve' => function (array $probability) : float {
                        return $probability['value'];
                    },
                ],
            ],
        ]);
    }
}
