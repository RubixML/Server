<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Serializers\JSON;
use Rubix\Server\Serializers\Binary;
use Rubix\Server\Serializers\Native;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface Controller
{
    public const SERIALIZER_HEADERS = [
        JSON::class => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        Native::class => [
            'Content-Type' => 'application/octet-stream',
            'Accept' => 'application/octet-stream',
        ],
        Binary::class => [
            'Content-Type' => 'application/octet-stream',
            'Accept' => 'application/octet-stream',
        ],
    ];

    /**
     * Handle the request.
     *
     * @param Request $request
     * @param array|null $params
     * @return Response
     */
    public function handle(Request $request, ?array $params) : Response;
}
