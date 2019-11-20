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
     * @var DataTracker
     */
    protected $dataTracker;

    /**
     * @return void
     */
    protected function boot(): void
    {
        $this->bind();

        $this->track();

        $this->listenForTerminating();
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
     * @return void
     */
    protected function track(): void
    {
        $this->app->make(ExecutionWatcher::class)->watch();

        $this->dataTracker = $this->app->make(DataTracker::class);
        $this->dataTracker->track();

        event(new Tracking());
    }

    /**
     * @return void
     */
    protected function listenForTerminating(): void
    {
        $this->app->afterBootstrapping(BootProviders::class, function () {
            $this->registerTerminating();
        });
    }

    /**
     * @return void
     */
    protected function registerTerminating(): void
    {
        $this->app->terminating(function () {
            event(new Terminating());

            $this->dataTracker->terminate();
            $this->app->make(DataProcessor::class)->process($this->dataTracker);
        });
    }
}
