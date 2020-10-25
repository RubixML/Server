<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Commands\Command;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Responses\ErrorResponse;
use Rubix\Server\Exceptions\ValidationException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use Exception;

use const Rubix\Server\Http\HTTP_OK;
use const Rubix\Server\Http\INTERNAL_SERVER_ERROR;

class RPCController implements Controller
{
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

            $status = HTTP_OK;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = INTERNAL_SERVER_ERROR;
        }

        $data = $this->serializer->serialize($response);

        return new ReactResponse($status, $this->serializer->headers(), $data);
    }
}
