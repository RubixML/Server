<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\ServerInfo;
use Rubix\Server\Models\Versions;
use GraphQL\Type\Definition\Type;

class ServerInfoType extends ObjectType
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
            'fields' => [
                'start' => [
                    'type' => Type::int(),
                    'resolve' => function (ServerInfo $info) : int {
                        return $info->start();
                    },
                ],
                'pid' => [
                    'type' => Type::int(),
                    'resolve' => function (ServerInfo $info) : int {
                        return $info->pid();
                    },
                ],
                'versions' => [
                    'type' => VersionsType::singleton(),
                    'resolve' => function (ServerInfo $info) : Versions {
                        return $info->versions();
                    },
                ],
            ],
        ]);
    }
}
