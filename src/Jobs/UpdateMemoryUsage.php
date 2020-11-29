<?php

namespace Rubix\Server\Jobs;

use Rubix\Server\Models\Memory;

use function memory_get_usage;
use function memory_get_peak_usage;

class UpdateMemoryUsage implements Job
{
    /**
     * The memory model.
     *
     * @var \Rubix\Server\Models\Memory
     */
    protected $memory;

    /**
     * @param \Rubix\Server\Models\Memory $memory
     */
    public function __construct(Memory $memory)
    {
        $this->memory = $memory;
    }

    /**
     * Run the job.
     */
    public function __invoke() : void
    {
        $current = memory_get_usage();
        $peak = memory_get_peak_usage();

        $this->memory->updateUsage($current, $peak);
    }
}
