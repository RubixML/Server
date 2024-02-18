<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\File;
use Rubix\Server\Services\Caches\Cache;
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
    protected string $basePath;

    /**
     * The cache.
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * @param string $basePath
     * @param Cache $cache
     * @throws RuntimeException
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
     * @return array<mixed>
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
            '/app.js' => [
                'GET' => [
                    [$this, 'serveCompressedFile'],
                    [$this, 'serveFile'],
                ],
            ],
            '/sw.js' => [
                'GET' => [
                    [$this, 'serveCompressedFile'],
                    [$this, 'serveFile'],
                ],
            ],
            '/app.css' => [
                'GET' => [
                    [$this, 'serveCompressedFile'],
                    [$this, 'serveFile'],
                ],
            ],
            '/manifest.json' => ['GET' => [$this, 'serveFile']],
            '/images/app-icon-small.png' => ['GET' => [$this, 'serveFile']],
            '/images/app-icon-apple-touch.png' => ['GET' => [$this, 'serveFile']],
            '/images/app-icon-medium.png' => ['GET' => [$this, 'serveFile']],
            '/images/app-icon-large.png' => ['GET' => [$this, 'serveFile']],
            '/fonts/Roboto-300.woff2' => ['GET' => [$this, 'serveFile']],
            '/fonts/Roboto-300.woff' => ['GET' => [$this, 'serveFile']],
            '/fonts/Roboto-regular.woff2' => ['GET' => [$this, 'serveFile']],
            '/fonts/Roboto-regular.woff' => ['GET' => [$this, 'serveFile']],
            '/fonts/Roboto-500.woff2' => ['GET' => [$this, 'serveFile']],
            '/fonts/Roboto-500.woff' => ['GET' => [$this, 'serveFile']],
            '/fonts/fa-solid-900.woff2' => ['GET' => [$this, 'serveFile']],
            '/fonts/fa-solid-900.woff' => ['GET' => [$this, 'serveFile']],
            '/sounds/sharp.ogg' => ['GET' => [$this, 'serveFile']],
        ];
    }

    /**
     * Respond with the web UI entry point.
     *
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function serveAppShell(ServerRequestInterface $request)
    {
        return $this->respondWithFile('/app.html');
    }

    /**
     * Serve a compressed version of a requested file if supported by the client.
     *
     * @param ServerRequestInterface $request
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
     * Serve a file.
     *
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function serveFile(ServerRequestInterface $request)
    {
        return $this->respondWithFile($request->getUri()->getPath());
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
}
