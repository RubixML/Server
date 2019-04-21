<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Commands\Command;
use Rubix\Server\Responses\ErrorResponse;
use Rubix\Server\Serializers\Serializer;
use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RuntimeException;
use Exception;

class ProbabilitiesController implements Controller
{
    /**
     * The command bus.
     *
     * @var \Rubix\Server\CommandBus
     */
    protected $commandBus;

    /**
     * The message serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * The headers to send with every response.
     *
     * @var array
     */
    protected $headers;

    /**
     * @param \Rubix\Server\CommandBus $commandBus
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(CommandBus $commandBus, Serializer $serializer)
    {
        $this->commandBus = $commandBus;
        $this->serializer = $serializer;
        $this->headers = Controller::SERIALIZER_HEADERS[get_class($serializer)];
    }

    /**
     * Handle the request.
     *
     * @param Request $request
     * @param array|null $params
     * @return Response
     */
    public function handle(Request $request, ?array $params = null) : Response
    {
        try {
            $data = $request->getBody()->getContents();

            $command = $this->serializer->unserialize($data);

            if (!$command instanceof Command) {
                throw new RuntimeException('Command could not be reconstituted.');
            }

            $response = $this->commandBus->dispatch($command);

            $status = 200;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = 500;
        }

        $data = $this->serializer->serialize($response);

        return new ReactResponse($status, $this->headers, $data);
    }
}
