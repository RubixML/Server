<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\Memory;
use Rubix\Server\GraphQL\Scalars\LongIntegerScalar;
use GraphQL\Type\Definition\Type;

class MemoryObject extends ObjectType
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
            'name' => 'Memory',
            'description' => 'Memory usage statistics.',
            'fields' => [
                'current' => [
                    'description' => 'The current memory usage of the server.',
                    'type' => Type::nonNull(LongIntegerScalar::singleton()),
                    'resolve' => function (Memory $memory) : int {
                        return $memory->current();
                    },
                ],
                'peak' => [
                    'description' => 'The maximum amount of memory consumed by the server so far.',
                    'type' => Type::nonNull(LongIntegerScalar::singleton()),
                    'resolve' => function (Memory $memory) : int {
                        return $memory->peak();
                    },
                ],
            ],
        ]);
    }
}
