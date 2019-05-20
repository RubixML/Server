<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Responses\ErrorResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Response as ReactResponse;
use Exception;

class QueryModelController extends RESTController
{
    /**
     * Handle the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array|null $params
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request, ?array $params = null) : Response
    {
        try {
            $response = $this->bus->dispatch(new QueryModel());

            $status = 200;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = 500;
        }

        return new ReactResponse($status, self::HEADERS, json_encode($response->asArray()));
    }
}
