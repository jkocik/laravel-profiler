<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Contracts\Memory;
use JKocik\Laravel\Profiler\Events\ProfilerBound;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionWatcher;
use JKocik\Laravel\Profiler\Services\Performance\TimerService;
use JKocik\Laravel\Profiler\Services\Performance\MemoryService;
use JKocik\Laravel\Profiler\LaravelExecution\LaravelExecutionData;

class LaravelProfiler extends BaseProfiler
{
    /**
     * @return void
     */
    protected function boot(): void
    {
        $this->bind();

        $this->initTracking();
    }

    /**
     * @return void
     */
    protected function bind(): void
    {
        $this->app->bind(DataTracker::class, LaravelDataTracker::class);

        $this->app->bind(DataProcessor::class, LaravelDataProcessor::class);

        $this->app->bind(ExecutionWatcher::class, LaravelExecutionWatcher::class);

        $this->app->singleton(ExecutionData::class, function ($app) {
            return $app->make(LaravelExecutionData::class);
        });

        $this->app->singleton(Timer::class, function ($app) {
            return $app->make(TimerService::class);
        });

        $this->app->singleton(Memory::class, function ($app) {
            return $app->make(MemoryService::class);
        });

        $this->app['events']->fire(ProfilerBound::class);
    }

    /**
     * @return void
     */
    protected function initTracking(): void
    {
        $this->app->make(Timer::class)->startLaravel();
        $this->app->make(ExecutionWatcher::class)->watch();

        $dataTracker = $this->app->make(DataTracker::class);
        $dataTracker->track();
        $this->app->terminating(function () use ($dataTracker) {
            $this->app->make(Memory::class)->recordPeak();

            $timer = $this->app->make(Timer::class);
            $timer->finish('response');
            $timer->finishLaravel();

            $dataTracker->terminate();
            $this->app->make(DataProcessor::class)->process($dataTracker);
        });
    }
}
