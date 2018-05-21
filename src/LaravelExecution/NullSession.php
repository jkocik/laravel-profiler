<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionSession;

class NullSession implements ExecutionSession
{
    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return Collection::make();
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return Collection::make();
    }
}
