<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Commands\Command;
use Rubix\Server\Serializers\Json;
use Rubix\Server\Serializers\Native;
use Rubix\Server\Serializers\Binary;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Responses\ErrorResponse;
use Rubix\Server\Exceptions\ValidationException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Response as ReactResponse;
use Exception;

class RPCController implements Controller
{
    public const SERIALIZER_HEADERS = [
        Json::class => [
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
     * The command bus.
     *
     * @var \Rubix\Server\CommandBus
     */
    protected $bus;

    /**
     * The message serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * The headers to send with each HTTP response.
     *
     * @var string[]
     */
    protected $headers;

    /**
     * @param \Rubix\Server\CommandBus $bus
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(CommandBus $bus, Serializer $serializer)
    {
        $this->bus = $bus;
        $this->serializer = $serializer;
        $this->headers = self::SERIALIZER_HEADERS[get_class($serializer)] ?? [];
    }

    /**
     * Handle the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param mixed[]|null $params
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request, ?array $params = null) : Response
    {
        try {
            $payload = $request->getBody()->getContents();

            $command = $this->serializer->unserialize($payload);

            if (!$command instanceof Command) {
                throw new ValidationException('Command could not be reconstituted.');
            }

            $response = $this->bus->dispatch($command);

            $status = self::OK;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = self::INTERNAL_SERVER_ERROR;
        }

        $data = $this->serializer->serialize($response);

        return new ReactResponse($status, $this->headers, $data);
    }
}
