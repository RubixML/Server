<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\Score;
use Rubix\Server\Helpers\JSON;
use Rubix\Server\Payloads\ErrorPayload;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use Exception;

use const Rubix\Server\Http\HTTP_OK;

class ScoresController extends RESTController
{
    /**
     * Handle the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request) : Response
    {
        try {
            $payload = JSON::decode($request->getBody()->getContents());

            $command = Score::fromArray($payload);

            $response = $this->bus->dispatch($command);

            $status = HTTP_OK;
        } catch (Exception $exception) {
            $response = ErrorPayload::fromException($exception);

            $status = $exception->getCode();
        }

        $data = JSON::encode($response->asArray());

        return new ReactResponse($status, self::HEADERS, $data);
    }
}
