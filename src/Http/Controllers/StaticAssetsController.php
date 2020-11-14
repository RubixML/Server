<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Helpers\MIME;
use Rubix\Server\Http\Responses\Success;
use Rubix\Server\Http\Responses\NotFound;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Filesystem\FilesystemInterface;
use Exception;

class StaticAssetsController extends Controller
{
    public const ASSETS_PATH = __DIR__ . '/assets';

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
     * Handle the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request) : Response
    {
        $path = $request->getUri()->getPath();

        $file = $this->filesystem->file(self::ASSETS_PATH . $path);

        return $file->exists()->then(function () use ($file) {
            return $file->getContents()->then(function ($data) use ($file) {
                return new Success([
                    'Content-Type' => MIME::guess($file),
                ], $data);
            });
        }, function (Exception $exception) {
            return new NotFound();
        });
    }
}
