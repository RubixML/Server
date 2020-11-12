<?php

namespace Rubix\Server\Http\Requests;

use Rubix\Server\Commands\Command;
use Rubix\Server\Serializers\Serializer;

class CommandRequest extends Request
{
    /**
     * @param \Rubix\Server\Commands\Command $command
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(Command $command, Serializer $serializer)
    {
        $data = $serializer->serialize($command);

        parent::__construct('POST', '/commands', $serializer->headers(), $data);
    }
}
