<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Services\QueryBus;
use Rubix\Server\Payloads\Payload;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\BadRequest;
use Rubix\Server\HTTP\Responses\UnsupportedMediaType;
use Rubix\Server\HTTP\Responses\InternalServerError;
use Rubix\Server\HTTP\Responses\UnprocessableEntity;
use Rubix\Server\Helpers\JSON;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

abstract class RESTController implements Controller
{
    public const HEADERS = [
        'Content-Type' => 'application/json',
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
        $contentType = $request->getHeaderLine('Content-Type');

        $acceptedContentType = self::HEADERS['Content-Type'];

        if ($contentType !== $acceptedContentType) {
            return new UnsupportedMediaType($acceptedContentType);
        }

        try {
            $json = JSON::decode($request->getBody());
        } catch (Exception $exception) {
            return new BadRequest(self::HEADERS, JSON::encode([
                'message' => $exception->getMessage(),
            ]));
        }

        $request = $request->withParsedBody($json);

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
        return new Success(self::HEADERS, JSON::encode($payload->asArray()));
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
        return new UnprocessableEntity(self::HEADERS, JSON::encode([
            'message' => $exception->getMessage(),
        ]));
    }
}
