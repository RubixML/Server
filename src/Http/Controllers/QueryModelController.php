<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\CommandBus;
use Rubix\Server\Commands\QueryModel;
use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class QueryModelController implements Controller
{
    const HEADERS = [
        'Content-Type' => 'text/json',
    ];

    /**
     * The command bus.
     * 
     * @var \Rubix\Server\CommandBus
     */
    protected $commandBus;

    /**
     * @param  \Rubix\Server\CommandBus  $commandBus
     * @return void
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * Handle the request.
     * 
     * @param  Request  $request
     * @param  array  $params
     * @return Response
     */
    public function handle(Request $request, array $params) : Response
    {
        try {
            $command = new QueryModel();

            $result = $this->commandBus->dispatch($command);
        } catch (Exception $e) {
            return new ReactResponse(500, self::HEADERS, json_encode([
                'error' => $e->getMessage(),
            ]));
        }

        return new ReactResponse(200, self::HEADERS, json_encode($result));
    }
}