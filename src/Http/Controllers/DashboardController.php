<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Services\QueryBus;
use Rubix\Server\Queries\GetServerStats;
use Rubix\Server\Http\Responses\Success;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class DashboardController extends RESTController
{
    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/server/dashboard' => [
                'GET' => [$this, 'getServerStats'],
            ],
        ];
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function getServerStats(ServerRequestInterface $request)
    {
        $json = (array) $request->getParsedBody();

        try {
            $query = GetServerStats::fromArray($json);
        } catch (Exception $exception) {
            $this->responseInvalid($exception);
        }

        return $this->queryBus->dispatch($query)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
    }
}
