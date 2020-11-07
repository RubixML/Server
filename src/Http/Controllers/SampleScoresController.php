<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\ScoreSample;
use Rubix\Server\Helpers\JSON;
use Rubix\Server\Responses\ErrorResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use Exception;

use const Rubix\Server\Http\HTTP_OK;

class SampleScoresController extends RESTController
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

            $command = ScoreSample::fromArray($payload);

            $response = $this->bus->dispatch($command);

            $status = HTTP_OK;
        } catch (Exception $exception) {
            $response = ErrorResponse::fromException($exception);

            $status = $exception->getCode();
        }

        $data = JSON::encode($response->asArray());

        return new ReactResponse($status, self::HEADERS, $data);
    }
}
