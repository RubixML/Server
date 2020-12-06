<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\HTTPStats;
use GraphQL\Type\Definition\Type;

class TransfersType extends ObjectType
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
            'description' => 'Transfer statistics.',
            'fields' => [
                'received' => [
                    'description' => 'The number of bytes that have been received in request bodies so far.',
                    'type' => Type::int(),
                    'resolve' => function (HTTPStats $httpStats) : int {
                        return $httpStats->bytesReceived();
                    },
                ],
                'sent' => [
                    'description' => 'The number of bytes that have been sent in response bodies so far.',
                    'type' => Type::int(),
                    'resolve' => function (HTTPStats $httpStats) : int {
                        return $httpStats->bytesSent();
                    },
                ],
            ],
        ]);
    }
}
