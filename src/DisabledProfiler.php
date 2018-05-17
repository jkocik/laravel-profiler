<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\Profiler;

class DisabledProfiler implements Profiler
{
    /**
     * @return void
     */
    public function boot(): void
    {

    }
}
