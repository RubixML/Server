<?php

namespace Rubix\Server\Jobs;

use Rubix\Server\Services\InMemoryCache;

class EvictCacheItems implements Job
{
    /**
     * The cache whose items to evict.
     *
     * @var \Rubix\Server\Services\InMemoryCache
     */
    protected $cache;

    /**
     * @param \Rubix\Server\Services\InMemoryCache $cache
     */
    public function __construct(InMemoryCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Run the job.
     */
    public function __invoke() : void
    {
        $this->cache->evict();
    }
}
