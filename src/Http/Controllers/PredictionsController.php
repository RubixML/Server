<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\Predict;
use Rubix\Server\Exceptions\ValidationException;
use Rubix\Server\Responses\ErrorResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use Exception;

class PredictionsController extends RESTController
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
            $payload = json_decode($request->getBody()->getContents(), true);

            if (empty($payload['samples'])) {
                throw new ValidationException('Samples property cannot be empty.');
            }

            $response = $this->bus->dispatch(Predict::fromArray($payload));

            $status = self::OK;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = self::INTERNAL_SERVER_ERROR;
        }

        $data = json_encode($response->asArray()) ?: '';

        return new ReactResponse($status, self::HEADERS, $data);
    }
}
