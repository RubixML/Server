<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\File;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use React\Filesystem\FilesystemInterface;
use React\Promise\PromiseInterface;
use Exception;

class StaticAssetsController implements Controller
{
    protected const ASSETS_PATH = '../../assets';

    protected const CACHE_MAX_AGE = 'max-age=604800';

    /**
     * The filesystem.
     *
     * @var \React\Filesystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @param \React\Filesystem\FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

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
     * @return \React\Promise\PromiseInterface
     */
    public function app(ServerRequestInterface $request) : PromiseInterface
    {
        return $this->respondWithFile('/app.html');
    }

    /**
     * @param string $path
     */
    public function respondWithFile(string $path) : PromiseInterface
    {
        $file = $this->filesystem->file(self::ASSETS_PATH . $path);

        return $file->exists()->then(function () use ($file) {
            return $file->getContents()->then(function ($data) use ($file) {
                return new Success([
                    'Content-Type' => File::mime($file),
                    'Cache-Control' => self::CACHE_MAX_AGE,
                ], $data);
            });
        }, function (Exception $exception) {
            return new NotFound();
        });
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request) : PromiseInterface
    {
        return $this->respondWithFile($request->getUri()->getPath());
    }
}
