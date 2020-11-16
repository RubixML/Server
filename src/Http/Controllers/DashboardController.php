<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Http\Responses\Success;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController extends RESTController
{
    /**
     * The dashboard model.
     *
     * @var \Rubix\Server\Models\Dashboard
     */
    protected $dashboard;

    /**
     * @param \React\Filesystem\FilesystemInterface $filesystem
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/server/dashboard' => [
                'GET' => [$this, 'getStats'],
            ],
        ];
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function getStats(Request $request)
    {
        return new Success(self::HEADERS, JSON::encode($this->dashboard));
    }
}
