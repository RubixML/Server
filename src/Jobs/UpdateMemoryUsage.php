<?php

namespace Rubix\Server\Jobs;

use Rubix\Server\Models\Memory;

class UpdateMemoryUsage implements Job
{
    /**
     * The memory model.
     *
     * @var Memory
     */
    protected Memory $memory;

    /**
     * @param Memory $memory
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
        $this->memory->updateUsage();
    }
}
