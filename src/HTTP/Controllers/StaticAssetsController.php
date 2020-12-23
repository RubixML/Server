<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\File;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\NotFound;
use Rubix\Server\HTTP\Responses\InternalServerError;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\Promise;

use function React\Promise\resolve;
use function is_file;
use function is_readable;
use function file_get_contents;
use function in_array;

class StaticAssetsController extends Controller
{
    protected const DEFAULT_HEADERS = [
        'Cache-Control' => 'max-age=604800',
    ];

    protected const ASSETS_PATH = __DIR__ . '/../../../assets';

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/' => [
                'GET' => [$this, 'serveApp'],
            ],
            '/ui/dashboard' => [
                'GET' => [$this, 'serveApp'],
            ],
            '/ui/visualizer/scatterplot' => [
                'GET' => [$this, 'serveApp'],
            ],
            '/ui/visualizer/line' => [
                'GET' => [$this, 'serveApp'],
            ],
            '/ui/visualizer/bubble' => [
                'GET' => [$this, 'serveApp'],
            ],
            '/app.js' => [
                'GET' => [
                    [$this, 'serveCompressedVersion'],
                    $this,
                ],
            ],
            '/sw.js' => [
                'GET' => [
                    [$this, 'serveCompressedVersion'],
                    $this,
                ],
            ],
            '/app.css' => [
                'GET' => [
                    [$this, 'serveCompressedVersion'],
                    $this,
                ],
            ],
            '/manifest.json' => ['GET' => $this],
            '/images/app-icon-small.png' => ['GET' => $this],
            '/images/app-icon-apple-touch.png' => ['GET' => $this],
            '/images/app-icon-medium.png' => ['GET' => $this],
            '/images/app-icon-large.png' => ['GET' => $this],
            '/fonts/Roboto-300.woff2' => ['GET' => $this],
            '/fonts/Roboto-300.woff' => ['GET' => $this],
            '/fonts/Roboto-regular.woff2' => ['GET' => $this],
            '/fonts/Roboto-regular.woff' => ['GET' => $this],
            '/fonts/Roboto-500.woff2' => ['GET' => $this],
            '/fonts/Roboto-500.woff' => ['GET' => $this],
            '/fonts/fa-solid-900.woff2' => ['GET' => $this],
            '/fonts/fa-solid-900.woff' => ['GET' => $this],
            '/sounds/sharp.ogg' => ['GET' => $this],
        ];
    }

    /**
     * Respond with the web UI entry point.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function serveApp(ServerRequestInterface $request)
    {
        return $this->respondWithFile('/app.html');
    }

    /**
     * Serve a compressed version of a requested file if supported by the client.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function serveCompressedVersion(ServerRequestInterface $request, callable $next)
    {
        if ($request->hasHeader('Accept-Encoding')) {
            $accept = preg_split('/\s*,\s*/', $request->getHeaderLine('Accept-Encoding')) ?: [];

            if (in_array('gzip', $accept)) {
                $path = $request->getUri()->getPath();

                $response = $this->respondWithFile("$path.gz");

                return resolve($response)->then(function (ResponseInterface $response) : ResponseInterface {
                    if ($response instanceof Success) {
                        $response = $response->withHeader('Content-Encoding', 'gzip');
                    }

                    return $response;
                });
            }
        }

        return $next($request);
    }

    /**
     * Respond with the contents of a file located in the assets folder.
     *
     * @param string $path
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    protected function respondWithFile(string $path)
    {
        $path = realpath(self::ASSETS_PATH . $path);

        if ($path === false or !is_file($path)) {
            return new NotFound();
        }

        if (!is_readable($path)) {
            return new InternalServerError();
        }

        return new Promise(function ($resolve) use ($path) {
            $data = file_get_contents($path) ?: '';

            $response = new Success([
                'Content-Type' => File::mime($path),
            ] + self::DEFAULT_HEADERS, $data);

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
