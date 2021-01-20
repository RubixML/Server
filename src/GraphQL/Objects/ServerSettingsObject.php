<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\ServerSettings;
use GraphQL\Type\Definition\Type;

class ServerSettingsObject extends ObjectType
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
            'name' => 'ServerSettings',
            'description' => 'The current user settings of the server.',
            'fields' => [
                'host' => [
                    'description' => 'The host address of the server.',
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => function (ServerSettings $settings) : string {
                        return $settings->host();
                    },
                ],
                'port' => [
                    'description' => 'The networking port the server is running on.',
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->port();
                    },
                ],
                'tls' => [
                    'description' => 'Is transport layer security (TLS) enabled?',
                    'type' => Type::nonNull(Type::boolean()),
                    'resolve' => function (ServerSettings $settings) : bool {
                        return $settings->tls();
                    },
                ],
                'maxConcurrentRequests' => [
                    'description' => 'The maximum number of requests to handle concurrently.',
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->maxConcurrentRequests();
                    },
                ],
                'memoryLimit' => [
                    'description' => 'The maximum amount of memory the server is allowed to consume in bytes.',
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->memoryLimit();
                    },
                ],
                'postMaxSize' => [
                    'description' => 'The maximum size of a request body in bytes.',
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (ServerSettings $settings) : int {
                        return $settings->postMaxSize();
                    },
                ],
            ],
        ]);
    }
}
