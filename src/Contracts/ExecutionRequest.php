<?php

namespace JKocik\Laravel\Profiler\Contracts;

use Illuminate\Support\Collection;

interface ExecutionRequest
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
