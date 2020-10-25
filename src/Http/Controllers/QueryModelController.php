<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Responses\ErrorResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use Exception;

use const Rubix\Server\Http\HTTP_OK;
use const Rubix\Server\Http\INTERNAL_SERVER_ERROR;

class QueryModelController extends RESTController
{
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
            $response = $this->bus->dispatch(new QueryModel());

            $status = HTTP_OK;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = INTERNAL_SERVER_ERROR;
        }

        $data = json_encode($response->asArray()) ?: '';

        return new ReactResponse($status, self::HEADERS, $data);
    }
}
