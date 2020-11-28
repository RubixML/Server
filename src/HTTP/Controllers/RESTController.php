<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Services\QueryBus;
use Rubix\Server\Payloads\Payload;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\BadRequest;
use Rubix\Server\HTTP\Responses\UnsupportedContentEncoding;
use Rubix\Server\HTTP\Responses\UnsupportedContentType;
use Rubix\Server\HTTP\Responses\InternalServerError;
use Rubix\Server\HTTP\Responses\UnprocessableEntity;
use Rubix\Server\Helpers\JSON;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

abstract class RESTController implements Controller
{
    protected const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
    ];

    protected const ACCEPTED_CONTENT_TYPES = [
        'application/json',
    ];

    protected const ACCEPTED_CONTENT_ENCODINGS = [
        'gzip', 'deflate', 'identity',
    ];

    /**
     * The query bus.
     *
     * @var \Rubix\Server\Services\QueryBus
     */
    protected $queryBus;

    /**
     * @param \Rubix\Server\Services\QueryBus $queryBus
     */
    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

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

            if (!in_array($encoding, self::ACCEPTED_CONTENT_ENCODINGS)) {
                return new UnsupportedContentEncoding(self::ACCEPTED_CONTENT_ENCODINGS);
            }

            try {
                switch ($encoding) {
                    case 'gzip':
                        $body = gzdecode($request->getBody());

                        break 1;

                    case 'deflate':
                        $body = gzinflate($request->getBody());

                        break 1;

                    default:
                    case 'identity':
                        $body = $request->getBody();

                        break 1;
                }
            } catch (Exception $exception) {
                return new BadRequest(self::DEFAULT_HEADERS, JSON::encode([
                    'message' => $exception->getMessage(),
                ]));
            }

            $stream = Utils::streamFor($body);

            $request = $request->withBody($stream);
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

            if (!in_array($type, self::ACCEPTED_CONTENT_TYPES)) {
                return new UnsupportedContentType(self::ACCEPTED_CONTENT_TYPES);
            }

            try {
                switch ($type) {
                    default:
                    case 'application/json':
                        $body = JSON::decode($request->getBody());

                        break 1;
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

    /**
     * Send the payload in a successful response.
     *
     * @internal
     *
     * @param \Rubix\Server\Payloads\Payload $payload
     * @return \Rubix\Server\HTTP\Responses\Success
     */
    public function respondSuccess(Payload $payload) : Success
    {
        return new Success(self::DEFAULT_HEADERS, JSON::encode($payload->asArray()));
    }

    /**
     * Respond with an internal server error.
     *
     * @param \Exception $exception
     * @return \Rubix\Server\HTTP\Responses\InternalServerError
     */
    public function respondServerError(Exception $exception) : InternalServerError
    {
        return new InternalServerError();
    }

    /**
     * Respond with an unprocessable entity error.
     *
     * @param \Exception $exception
     * @return \Rubix\Server\HTTP\Responses\UnprocessableEntity
     */
    public function respondInvalid(Exception $exception) : UnprocessableEntity
    {
        return new UnprocessableEntity(self::DEFAULT_HEADERS, JSON::encode([
            'message' => $exception->getMessage(),
        ]));
    }
}
