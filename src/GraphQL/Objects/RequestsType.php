<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\HTTPStats;
use GraphQL\Type\Definition\Type;

class RequestsType extends ObjectType
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
                'successful' => [
                    'type' => Type::int(),
                    'resolve' => function (HTTPStats $httpStats) : int {
                        return $httpStats->numSuccessful();
                    },
                ],
                'rejected' => [
                    'type' => Type::int(),
                    'resolve' => function (HTTPStats $httpStats) : int {
                        return $httpStats->numRejected();
                    },
                ],
                'failed' => [
                    'type' => Type::int(),
                    'resolve' => function (HTTPStats $httpStats) : int {
                        return $httpStats->numFailed();
                    },
                ],
            ],
        ]);
    }
}
