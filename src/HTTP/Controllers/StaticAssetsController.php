<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\File;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\NotFound;
use Rubix\Server\HTTP\Responses\InternalServerError;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;

use function is_file;
use function is_readable;
use function file_get_contents;

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
            '/' => ['GET' => [$this, 'app']],
            '/ui/dashboard' => ['GET' => [$this, 'app']],
            '/ui/visualizer' => ['GET' => [$this, 'app']],
            '/ui/visualizer/scatterplot' => ['GET' => [$this, 'app']],
            '/app.js' => ['GET' => $this],
            '/app.css' => ['GET' => $this],
            '/sw.js' => ['GET' => $this],
            '/manifest.json' => ['GET' => $this],
            '/images/app-icon-small.png' => ['GET' => $this],
            '/images/app-icon-apple-touch.png' => ['GET' => $this],
            '/images/app-icon-medium.png' => ['GET' => $this],
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
