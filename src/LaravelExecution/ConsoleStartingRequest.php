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
        return collect([
            'type' => 'command',
            'method' => null,
            'path' => null,
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return collect();
    }
}
