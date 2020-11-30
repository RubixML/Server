<?php

namespace Rubix\Server\Models;

use const Rubix\Server\VERSION as SERVER_VERSION;
use const Rubix\ML\VERSION as ML_VERSION;

class Versions extends Model
{
    /**
     * Return the Server library version.
     *
     * @return string
     */
    public function server() : string
    {
        return SERVER_VERSION;
    }

    /**
     * Return the ML library version.
     *
     * @return string
     */
    public function ml() : string
    {
        return ML_VERSION;
    }

    /**
     * Return the version of PHP the server is running on.
     *
     * @return string
     */
    public function php() : string
    {
        return phpversion() ?: 'unknown';
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'server' => $this->server(),
            'ml' => $this->ml(),
            'php' => $this->php(),
        ];
    }
}
