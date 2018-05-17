<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface Profiler
{
    /**
     * @return void
     */
    public function boot(): void;
}
