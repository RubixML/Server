<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Responses\ErrorResponse;
use Rubix\Server\Serializers\Json;
use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class QueryModelController implements Controller
{
    protected const HEADERS = [
        'Content-Type' => 'text/json',
    ];

    /**
     * The command bus.
     *
     * @var \Rubix\Server\CommandBus
     */
    protected $commandBus;

    /**
     * The JSON message serializer.
     *
     * @var \Rubix\Server\Serializers\Json
     */
    protected $serializer;

    /**
     * @param \Rubix\Server\CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        $this->serializer = new Json();
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
            $response = $this->commandBus->dispatch(new QueryModel());

            $status = 200;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = 500;
        }

        $data = $this->serializer->serialize($response);

        return new ReactResponse($status, self::HEADERS, $data);
    }
}
