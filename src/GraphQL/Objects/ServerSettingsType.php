<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\ServerSettings;
use GraphQL\Type\Definition\Type;

class ServerSettingsType extends ObjectType
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
                'host' => [
                    'type' => Type::string(),
                    'resolve' => function (ServerSettings $settings) : string {
                        return $settings->host();
                    },
                ],
                'port' => [
                    'type' => Type::int(),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->port();
                    },
                ],
                'maxConcurrentRequests' => [
                    'type' => Type::int(),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->maxConcurrentRequests();
                    },
                ],
                'sseReconnectBuffer' => [
                    'type' => Type::int(),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->sseReconnectBuffer();
                    },
                ],
                'memoryLimit' => [
                    'type' => Type::int(),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->memoryLimit();
                    },
                ],
                'postMaxSize' => [
                    'type' => Type::int(),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->postMaxSize();
                    },
                ],
            ],
        ]);
    }
}
