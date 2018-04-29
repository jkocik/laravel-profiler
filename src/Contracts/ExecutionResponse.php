<?php

namespace JKocik\Laravel\Profiler\Contracts;

use Illuminate\Support\Collection;

interface ExecutionResponse
{
    /**
     * @return Collection
     */
    public function meta(): Collection;

    /**
     * @return Collection
     */
    public function data(): Collection;
}
