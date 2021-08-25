<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Rubix\Server\Helpers\JSON;
use Rubix\Server\HTTP\Responses\BadRequest;
use Rubix\Server\HTTP\Responses\UnsupportedContentType;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class ParseRequestBody
{
    protected const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
    ];

    /**
     * Parse the request body content.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($request->hasHeader('Content-Type')) {
            $type = $request->getHeaderLine('Content-Type');

            try {
                switch ($type) {
                    case 'application/json':
                        $body = JSON::decode($request->getBody());

                        break;

                    default:
                        return new UnsupportedContentType([
                            'application/json',
                        ]);
                }
            } catch (Exception $exception) {
                return new BadRequest(self::DEFAULT_HEADERS, JSON::encode([
                    'error' => [
                        'message' => $exception->getMessage(),
                    ],
                ]));
            }

            $request = $request->withParsedBody($body);
        }

        return $next($request);
    }
}
