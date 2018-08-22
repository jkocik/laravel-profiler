<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionContent;

class NullContent implements ExecutionContent
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
