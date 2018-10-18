<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Services\Performance\NullTimerService;

class DisabledProfiler extends BaseProfiler
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(Timer::class, function ($app) {
            return $app->make(NullTimerService::class);
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {

    }
}
