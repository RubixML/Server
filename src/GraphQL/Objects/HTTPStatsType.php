<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\HTTPStats;

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
                    'type' => RequestsType::singleton(),
                    'resolve' => function (HTTPStats $httpStats) : HTTPStats {
                        return $httpStats;
                    },
                ],
                'transfers' => [
                    'type' => TransfersType::singleton(),
                    'resolve' => function (HTTPStats $httpStats) : HTTPStats {
                        return $httpStats;
                    },
                ],
            ],
        ]);
    }
}
