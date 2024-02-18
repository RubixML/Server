<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\JSON;
use Rubix\Server\HTTP\Responses\BadRequest;
use Rubix\Server\HTTP\Responses\UnprocessableEntity;
use Exception;

abstract class JSONController extends Controller
{
    protected const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
    ];

    /**
     * Respond with bad request.
     *
     * @param Exception $exception
     * @return BadRequest
     */
    public function respondWithBadRequest(Exception $exception) : BadRequest
    {
        return new BadRequest(self::DEFAULT_HEADERS, JSON::encode([
            'error' => [
                'message' => $exception->getMessage(),
            ],
        ]));
    }

    /**
     * Respond with unprocessable entity.
     *
     * @param Exception $exception
     * @return UnprocessableEntity
     */
    public function respondWithUnprocessable(Exception $exception) : UnprocessableEntity
    {
        return new UnprocessableEntity(self::DEFAULT_HEADERS, JSON::encode([
            'error' => [
                'message' => $exception->getMessage(),
            ],
        ]));
    }
}
