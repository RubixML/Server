<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\HTTPStats;
use Rubix\Server\GraphQL\Scalars\LongIntegerScalar;
use GraphQL\Type\Definition\Type;

class RequestsObject extends ObjectType
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
            'name' => 'Requests',
            'description' => 'Request statistics.',
            'fields' => [
                'successful' => [
                    'description' => 'The number of successful requests handled by the server.',
                    'type' => Type::nonNull(LongIntegerScalar::singleton()),
                    'resolve' => function (HTTPStats $httpStats) : int {
                        return $httpStats->numSuccessful();
                    },
                ],
                'rejected' => [
                    'description' => 'The number of requests that were rejected due to a 400 level error.',
                    'type' => Type::nonNull(LongIntegerScalar::singleton()),
                    'resolve' => function (HTTPStats $httpStats) : int {
                        return $httpStats->numRejected();
                    },
                ],
                'failed' => [
                    'description' => 'The number of requests that failed due to a 500 level error.',
                    'type' => Type::nonNull(LongIntegerScalar::singleton()),
                    'resolve' => function (HTTPStats $httpStats) : int {
                        return $httpStats->numFailed();
                    },
                ],
            ],
        ]);
    }
}
