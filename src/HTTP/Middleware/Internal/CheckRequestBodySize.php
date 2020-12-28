<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Rubix\Server\Models\ServerSettings;
use Rubix\Server\HTTP\Responses\PayloadTooLarge;
use Psr\Http\Message\ServerRequestInterface;

class CheckRequestBodySize
{
    /**
     * The server settings model.
     *
     * @var \Rubix\Server\Models\ServerSettings
     */
    protected $settings;

    /**
     * @param \Rubix\Server\Models\ServerSettings $settings
     */
    public function __construct(ServerSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Dispatch events related to the request/response cycle.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($request->hasHeader('Content-Length')) {
            $size = (int) $request->getHeaderLine('Content-Length');

            if ($size > $this->settings->postMaxSize()) {
                return new PayloadTooLarge();
            }
        }

        return $next($request);
    }
}
