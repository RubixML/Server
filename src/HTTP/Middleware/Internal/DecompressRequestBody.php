<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Rubix\Server\Helpers\JSON;
use Rubix\Server\HTTP\Responses\BadRequest;
use Rubix\Server\HTTP\Responses\UnsupportedContentEncoding;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Utils;
use Exception;

use function gzdecode;
use function gzinflate;

class DecompressRequestBody
{
    protected const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
    ];

    /**
     * Decompress the request body.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($request->hasHeader('Content-Encoding')) {
            $encoding = $request->getHeaderLine('Content-Encoding');

            try {
                switch ($encoding) {
                    case 'gzip':
                        $data = gzdecode($request->getBody());

                        break;

                    case 'deflate':
                        $data = gzinflate($request->getBody());

                        break;

                    case 'identity':
                        $data = $request->getBody();

                        break;

                    default:
                        return new UnsupportedContentEncoding([
                            'gzip', 'deflate', 'identity',
                        ]);
                }
            } catch (Exception $exception) {
                return new BadRequest(self::DEFAULT_HEADERS, JSON::encode([
                    'error' => [
                        'message' => $exception->getMessage(),
                    ],
                ]));
            }

            $request = $request->withBody(Utils::streamFor($data));
        }

        return $next($request);
    }
}
