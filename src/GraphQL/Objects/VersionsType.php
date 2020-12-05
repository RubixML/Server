<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\Versions;
use GraphQL\Type\Definition\Type;

class VersionsType extends ObjectType
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
                'server' => [
                    'type' => Type::string(),
                    'resolve' => function (Versions $versions) : string {
                        return $versions->server();
                    },
                ],
                'ml' => [
                    'type' => Type::string(),
                    'resolve' => function (Versions $versions) : string {
                        return $versions->ml();
                    },
                ],
                'php' => [
                    'type' => Type::string(),
                    'resolve' => function (Versions $versions) : string {
                        return $versions->php();
                    },
                ],
            ],
        ]);
    }
}
