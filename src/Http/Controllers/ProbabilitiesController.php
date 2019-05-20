<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\Proba;
use Rubix\Server\Exceptions\ValidationException;
use Rubix\Server\Responses\ErrorResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Response as ReactResponse;
use Exception;

class ProbabilitiesController extends RESTController
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
            $payload = json_decode($request->getBody()->getContents());

            if (empty($payload->samples)) {
                throw new ValidationException('Samples cannot be empty.');
            }

            $response = $this->bus->dispatch(new Proba($payload->samples));

            $status = 200;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = 500;
        }

        return new ReactResponse($status, self::HEADERS, json_encode($response->asArray()));
    }
}
