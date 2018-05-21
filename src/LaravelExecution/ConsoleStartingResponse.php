<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionResponse;

class ConsoleStartingResponse implements ExecutionResponse
{
    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return Collection::make([
            'status' => null,
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
