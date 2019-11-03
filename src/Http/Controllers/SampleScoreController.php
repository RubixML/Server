<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\RankSample;
use Rubix\Server\Exceptions\ValidationException;
use Rubix\Server\Responses\ErrorResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Response as ReactResponse;
use Exception;

class SampleScoreController extends RESTController
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
            $payload = json_decode($request->getBody()->getContents(), true);

            if (empty($payload['sample'])) {
                throw new ValidationException('Sample property cannot be empty.');
            }

            $response = $this->bus->dispatch(RankSample::fromArray($payload));

            $status = self::OK;
        } catch (Exception $e) {
            $response = new ErrorResponse($e->getMessage());

            $status = self::INTERNAL_SERVER_ERROR;
        }

        return new ReactResponse($status, self::HEADERS, json_encode($response->asArray()));
    }
}