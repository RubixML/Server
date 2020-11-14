<?php

namespace Rubix\Server;

use Rubix\Server\Exceptions\RuntimeException;
use React\Filesystem\Node\FileInterface;

class MIME
{
    /**
     * Guess the MIME type from a file.
     *
     * @param \React\Filesystem\Node\FileInterface $file
     * @return string
     */
    public static function guess(FileInterface $file) : string
    {
        $pathInfo = pathinfo($file->getPath());

        switch ($pathInfo['extension']) {
            case 'html':
                return 'text/html';

            case 'js':
                return 'application/javascript';

            case 'json':
                return 'application/json';

            case 'css':
                return 'text/css';

            case 'woff':
                return 'font/woff';

            case 'woff2':
                return 'font/woff2';

            case 'png':
                return 'image/png';

            case 'svg':
                return 'image/svg+xml';

            case 'ogg':
                return 'audio/ogg';

            default:
                throw new RuntimeException('Could not guess file MIME type.');
        }
    }
}
