<?php

namespace Rubix\Server\Http\Requests;

use Rubix\Server\Helpers\JSON;

class JSONRequest extends Request
{
    public const HEADERS = [
        'Content-Type' => 'application/json',
    ];

    /**
     * @param string $method
     * @param string $path
     * @param mixed[] $json
     */
    public function __construct(string $method, string $path, array $json)
    {
        parent::__construct($method, $path, self::HEADERS, JSON::encode($json));
    }
}
