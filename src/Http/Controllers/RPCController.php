<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Serializers\Serializer;

abstract class RPCController extends Controller
{
    /**
     * The message serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }
}
