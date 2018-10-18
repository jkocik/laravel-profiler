<?php

namespace JKocik\Laravel\Profiler\Services\Performance;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Memory;

class MemoryService implements Memory
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Collection
     */
    protected $memory;

    /**
     * MemoryService constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->memory = Collection::make();
    }

    /**
     * @return void
     */
    public function recordPeak(): void
    {
        $this->memory->put('peak', memory_get_peak_usage());
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->memory;
    }
}
