<?php

namespace JKocik\Laravel\Profiler\Contracts;

use Illuminate\Support\Collection;

interface DataTracker
{
    /**
     * @return void
     */
    public function track(): void;

    /**
     * @return void
     */
    public function terminate(): void;

    /**
     * @return Collection
     */
    public function meta(): Collection;

    /**
     * @return Collection
     */
    public function data(): Collection;
}
