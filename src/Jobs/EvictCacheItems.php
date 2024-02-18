<?php

namespace Rubix\Server\Jobs;

use Rubix\Server\Services\Caches\Cache;

class EvictCacheItems implements Job
{
    /**
     * The cache whose items to evict.
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
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
