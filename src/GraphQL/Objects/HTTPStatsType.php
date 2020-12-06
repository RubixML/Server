<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\HTTPStats;
use GraphQL\Type\Definition\Type;

class HTTPStatsType extends ObjectType
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
            'description' => 'Statistics related to the HTTP request/response cycle.',
            'fields' => [
                'requests' => [
                    'type' => Type::nonNull(RequestsType::singleton()),
                    'resolve' => function (HTTPStats $httpStats) : HTTPStats {
                        return $httpStats;
                    },
                ],
                'transfers' => [
                    'type' => Type::nonNull(TransfersType::singleton()),
                    'resolve' => function (HTTPStats $httpStats) : HTTPStats {
                        return $httpStats;
                    },
                ],
            ],
        ]);
    }
}
