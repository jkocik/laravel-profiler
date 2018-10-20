<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Events\ProfilerBound;
use JKocik\Laravel\Profiler\Services\Performance\NullTimerService;

class DisabledProfiler extends BaseProfiler
{
    /**
     * @return void
     */
    protected function boot(): void
    {
        $this->bind();
    }

    /**
     * @return void
     */
    protected function bind(): void
    {
        $this->app->singleton(Timer::class, function ($app) {
            return $app->make(NullTimerService::class);
        });

        event(ProfilerBound::class);
    }
}
