<?php

namespace Rubix\Server\HTTP\Requests;

use Rubix\Server\Helpers\JSON;

class JSONRequest extends Request
{
    public const HEADERS = [
        'Content-Type' => 'application/json',
    ];

    /**
     * @param string $method
     * @param string $path
     * @param mixed[]|null $json
     */
    public function __construct(string $method, string $path, ?array $json = null)
    {
        parent::__construct($method, $path, self::HEADERS, JSON::encode($json));
    }
}
