<?php

namespace JKocik\Laravel\Profiler;

use ElephantIO\Client;
use Psr\Log\NullLogger;
use ElephantIO\EngineInterface;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionWatcher;
use JKocik\Laravel\Profiler\Services\Timer\TimerService;
use JKocik\Laravel\Profiler\Contracts\RequestHandledListener;
use JKocik\Laravel\Profiler\Services\BroadcastingEngineService;
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

        $this->app->bind(EngineInterface::class, BroadcastingEngineService::class);

        $this->app->bind(Client::class, function ($app) {
            return new Client(
                $app->make(EngineInterface::class),
                $app->make(NullLogger::class)
            );
        });

        $this->app->singleton(ExecutionData::class, function ($app) {
            return $app->make(LaravelExecutionData::class);
        });

        $this->app->singleton(Timer::class, function ($app) {
            return $app->make(TimerService::class);
        });

        $this->app->make(Timer::class)->startLaravel();
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $timer = $this->app->make(Timer::class);
        $dataTracker = $this->app->make(DataTracker::class);
        $dataProcessor = $this->app->make(DataProcessor::class);
        $executionWatcher = $this->app->make(ExecutionWatcher::class);

        $executionWatcher->watch();
        $dataTracker->track();
        $this->app->terminating(function () use ($timer, $dataTracker, $dataProcessor) {
            $timer->finishLaravel();
            $dataTracker->terminate();
            $dataProcessor->process($dataTracker);
        });
    }
}
