<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\Memory;
use GraphQL\Type\Definition\Type;

class MemoryType extends ObjectType
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
            'description' => 'Memory usage statistics.',
            'fields' => [
                'current' => [
                    'description' => 'The current memory usage of the server.',
                    'type' => Type::int(),
                    'resolve' => function (Memory $memory) : int {
                        return $memory->current();
                    },
                ],
                'peak' => [
                    'description' => 'The maximum amount of memory consumed by the server so far.',
                    'type' => Type::int(),
                    'resolve' => function (Memory $memory) : int {
                        return $memory->peak();
                    },
                ],
            ],
        ]);
    }
}
