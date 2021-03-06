<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\ProcessInfo;
use GraphQL\Type\Definition\Type;

class ProcessInfoObject extends ObjectType
{
    /**
     * The singleton instance of the object type.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * @return self
     */
    public static function singleton() : self
    {
        return self::$instance ?? self::$instance = new self([
            'name' => 'ProcessInfo',
            'description' => 'Information related to the status of the server.',
            'fields' => [
                'start' => [
                    'description' => 'The timestamp of when the server went up.',
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (ProcessInfo $info) : int {
                        return $info->start();
                    },
                ],
                'pid' => [
                    'description' => 'The process ID (PID) of the server.',
                    'type' => Type::int(),
                    'resolve' => function (ProcessInfo $info) : ?int {
                        return $info->pid();
                    },
                ],
            ],
        ]);
    }
}
