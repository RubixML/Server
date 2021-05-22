<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\HTTPStats;
use GraphQL\Type\Definition\Type;

class HTTPStatsObject extends ObjectType
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
            'name' => 'HTTPStats',
            'description' => 'Statistics related to the HTTP request/response cycle.',
            'fields' => [
                'requests' => [
                    'type' => Type::nonNull(RequestsObject::singleton()),
                    'resolve' => function (HTTPStats $httpStats) : HTTPStats {
                        return $httpStats;
                    },
                ],
                'transfers' => [
                    'type' => Type::nonNull(TransfersObject::singleton()),
                    'resolve' => function (HTTPStats $httpStats) : HTTPStats {
                        return $httpStats;
                    },
                ],
            ],
        ]);
    }
}
