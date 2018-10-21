<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Events\Tracking;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Contracts\Memory;
use JKocik\Laravel\Profiler\Events\Terminating;
use JKocik\Laravel\Profiler\Events\ProfilerBound;
use Illuminate\Foundation\Bootstrap\BootProviders;
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

        $dataTracker = $this->track();

        $this->listenForTerminating($dataTracker);
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

        event(new ProfilerBound());
    }

    /**
     * @return DataTracker
     */
    protected function track(): DataTracker
    {
        $this->app->make(ExecutionWatcher::class)->watch();

        $dataTracker = $this->app->make(DataTracker::class);
        $dataTracker->track();

        event(new Tracking());

        return $dataTracker;
    }

    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    protected function listenForTerminating(DataTracker $dataTracker): void
    {
        $this->app->afterBootstrapping(BootProviders::class, function () use ($dataTracker) {
            $this->registerTerminating($dataTracker);
        });
    }

    protected function registerTerminating(DataTracker $dataTracker): void
    {
        $this->app->terminating(function () use ($dataTracker) {
            event(new Terminating());

            $dataTracker->terminate();
            $this->app->make(DataProcessor::class)->process($dataTracker);
        });
    }
}
