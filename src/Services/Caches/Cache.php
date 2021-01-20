<?php

namespace Rubix\Server\Services\Caches;

interface Cache
{
    /**
     * Does the cache contain an item with a given key?
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool;

    /**
     * Put an item in the cache.
     *
     * @param string $key
     * @param mixed $data
     */
    public function put(string $key, $data) : void;

    /**
     * Retrieve an item from the store.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Remove expired cache items from the store.
     */
    public function evict() : void;

    /**
     * Flush all items out of the cache.
     */
    public function flush() : void;
}
