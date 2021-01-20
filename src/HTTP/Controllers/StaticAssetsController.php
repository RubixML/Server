<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\File;
use Rubix\Server\Services\Cache;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\NotFound;
use Rubix\Server\HTTP\Responses\InternalServerError;
use Rubix\Server\Exceptions\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\Promise;

use function React\Promise\resolve;
use function realpath;
use function is_dir;
use function is_readable;
use function file_get_contents;
use function in_array;
use function preg_split;

class StaticAssetsController extends Controller
{
    protected const DEFAULT_HEADERS = [
        'Cache-Control' => 'no-cache',
    ];

    /**
     * The full path to the assets folder without directory traversal.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The cache.
     *
     * @var \Rubix\Server\Services\Cache
     */
    protected $cache;

    /**
     * @param string $basePath
     * @param \Rubix\Server\Services\Cache $cache
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function __construct(string $basePath, Cache $cache)
    {
        $basePath = realpath($basePath);

        if (!$basePath or !is_dir($basePath)) {
            throw new RuntimeException("Static assets not found at $basePath.");
        }

        if (!is_readable($basePath)) {
            throw new RuntimeException('Static assets folder is not readable.');
        }

        $cache->flush();

        $this->basePath = $basePath;
        $this->cache = $cache;
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/ui' => [
                'GET' => [$this, 'serveAppShell'],
            ],
            '/ui/dashboard' => [
                'GET' => [$this, 'serveAppShell'],
            ],
            '/ui/visualizer/line' => [
                'GET' => [$this, 'serveAppShell'],
            ],
            '/ui/visualizer/bubble' => [
                'GET' => [$this, 'serveAppShell'],
            ],
            '/app.js' => [
                'GET' => [
                    [$this, 'serveCompressedFile'],
                    $this,
                ],
            ],
            '/sw.js' => [
                'GET' => [
                    [$this, 'serveCompressedFile'],
                    $this,
                ],
            ],
            '/app.css' => [
                'GET' => [
                    [$this, 'serveCompressedFile'],
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
    public function serveAppShell(ServerRequestInterface $request)
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
    public function serveCompressedFile(ServerRequestInterface $request, callable $next)
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
        $path = realpath($this->basePath . $path);

        if ($path === false) {
            return new NotFound();
        }

        if ($this->cache->has($path)) {
            return $this->cache->get($path);
        }

        if (strpos($path, $this->basePath) !== 0) {
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

            $this->cache->put($path, $response);

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
