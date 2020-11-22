<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Payloads\Payload;
use Rubix\Server\Payloads\ErrorPayload;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Http\Responses\Success;
use Rubix\Server\Http\Responses\BadRequest;
use Rubix\Server\Http\Responses\UnsupportedMediaType;
use Rubix\Server\Http\Responses\InternalServerError;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

abstract class RPCController implements Controller
{
    /**
     * The message serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
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

        $acceptedContentType = $this->serializer->mime();

        if ($contentType !== $acceptedContentType) {
            return new UnsupportedMediaType($acceptedContentType);
        }

        try {
            $message = $this->serializer->unserialize((string) $request->getBody());
        } catch (Exception $exception) {
            $payload = ErrorPayload::fromException($exception);

            $data = $this->serializer->serialize($payload);

            return new BadRequest($this->serializer->headers(), $data);
        }

        $request = $request->withParsedBody($message);

        return $next($request);
    }

    /**
     * Send the payload in a successful response.
     *
     * @param \Rubix\Server\Payloads\Payload $payload
     * @return \Rubix\Server\Http\Responses\Success
     */
    public function respondSuccess(Payload $payload) : Success
    {
        $data = $this->serializer->serialize($payload);

        return new Success($this->serializer->headers(), $data);
    }

    /**
     * Respond with an internal server error.
     *
     * @param \Exception $exception
     * @return \Rubix\Server\Http\Responses\InternalServerError
     */
    public function respondServerError(Exception $exception) : InternalServerError
    {
        return new InternalServerError();
    }
}
