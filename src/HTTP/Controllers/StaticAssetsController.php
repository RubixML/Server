<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\File;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;

use function is_readable;
use function file_get_contents;

class StaticAssetsController implements Controller
{
    protected const ASSETS_PATH = __DIR__ . '/../../../assets';

    protected const CACHE_MAX_AGE = 'max-age=604800';

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/' => ['GET' => [$this, 'app']],
            '/server' => ['GET' => [$this, 'app']],
            '/app.js' => ['GET' => $this],
            '/app.css' => ['GET' => $this],
            '/sw.js' => ['GET' => $this],
            '/manifest.json' => ['GET' => $this],
            '/images/app-icon-small.png' => ['GET' => $this],
            '/images/app-icon-large.png' => ['GET' => $this],
            '/fonts/fa-solid-900.woff' => ['GET' => $this],
            '/fonts/fa-solid-900.woff2' => ['GET' => $this],
            '/sounds/sharp.ogg' => ['GET' => $this],
        ];
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function app(ServerRequestInterface $request)
    {
        return $this->respondWithFile('/app.html');
    }

    /**
     * @param string $path
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    protected function respondWithFile(string $path)
    {
        $path = self::ASSETS_PATH . $path;

        if (!is_readable($path)) {
            return new NotFound();
        }

        return new Promise(function ($resolve) use ($path) {
            $data = file_get_contents($path) ?: null;

            $response = new Success([
                'Content-Type' => File::mime($path),
                'Cache-Control' => self::CACHE_MAX_AGE,
            ], $data);

            $resolve($response);
        });
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request)
    {
        return $this->respondWithFile($request->getUri()->getPath());
    }
}
