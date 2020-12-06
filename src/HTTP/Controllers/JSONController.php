<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\JSON;
use Rubix\Server\HTTP\Responses\BadRequest;
use Rubix\Server\HTTP\Responses\UnsupportedContentEncoding;
use Rubix\Server\HTTP\Responses\UnsupportedContentType;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Utils;
use Exception;

abstract class JSONController extends Controller
{
    protected const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
    ];

    /**
     * Decompress the request body.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function decompressRequestBody(ServerRequestInterface $request, callable $next)
    {
        if ($request->hasHeader('Content-Encoding')) {
            $encoding = $request->getHeaderLine('Content-Encoding');

            try {
                switch ($encoding) {
                    case 'gzip':
                        $data = gzdecode($request->getBody());

                        break 1;

                    case 'deflate':
                        $data = gzinflate($request->getBody());

                        break 1;

                    case 'identity':
                        $data = $request->getBody();

                        break 1;

                    default:
                        return new UnsupportedContentEncoding([
                            'gzip', 'deflate', 'identity',
                        ]);
                }
            } catch (Exception $exception) {
                return new BadRequest(self::DEFAULT_HEADERS, JSON::encode([
                    'message' => $exception->getMessage(),
                ]));
            }

            $request = $request->withBody(Utils::streamFor($data));
        }

        return $next($request);
    }

    /**
     * Parse the request body content.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function parseRequestBody(ServerRequestInterface $request, callable $next)
    {
        if ($request->hasHeader('Content-Type')) {
            $type = $request->getHeaderLine('Content-Type');

            try {
                switch ($type) {
                    case 'application/json':
                        $body = JSON::decode($request->getBody());

                        break 1;

                    default:
                        return new UnsupportedContentType([
                            'application/json',
                        ]);
                }
            } catch (Exception $exception) {
                return new BadRequest(self::DEFAULT_HEADERS, JSON::encode([
                    'message' => $exception->getMessage(),
                ]));
            }

            $request = $request->withParsedBody($body);
        }

        return $next($request);
    }
}
