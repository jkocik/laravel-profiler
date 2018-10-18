<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\Timer;
use Illuminate\Foundation\Bootstrap\BootProviders;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionWatcher;
use JKocik\Laravel\Profiler\Contracts\RequestHandledListener;
use JKocik\Laravel\Profiler\Services\Performance\TimerService;
use JKocik\Laravel\Profiler\LaravelExecution\LaravelExecutionData;

class LaravelProfiler extends BaseProfiler
{
    /**
     * @return void
     */
    public function register(): void
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

        $this->initPerformance();
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->app->make(ExecutionWatcher::class)->watch();

        $dataTracker = $this->app->make(DataTracker::class);
        $dataTracker->track();

        $this->app->terminating(function () use ($dataTracker) {
            $this->finishPerformance();
            $dataTracker->terminate();
            $this->app->make(DataProcessor::class)->process($dataTracker);
        });
    }

    /**
     * @return void
     */
    protected function initPerformance(): void
    {
        $timer = $this->app->make(Timer::class);
        $timer->startLaravel();

        $this->app->beforeBootstrapping(BootProviders::class, function () use ($timer) {
            $timer->start('bootstrap');
        });
        $this->app->afterBootstrapping(BootProviders::class, function () use ($timer) {
            $timer->finish('bootstrap');
        });
    }

    /**
     * @return void
     */
    protected function finishPerformance(): void
    {
        $this->app->make(Timer::class)->finishLaravel();
    }
}
