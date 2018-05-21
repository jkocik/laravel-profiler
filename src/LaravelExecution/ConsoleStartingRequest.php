<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;

class ConsoleStartingRequest implements ExecutionRequest
{
    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return Collection::make([
            'type' => 'command-starting',
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return Collection::make();
    }
}
