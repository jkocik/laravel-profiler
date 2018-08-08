<?php

namespace JKocik\Laravel\Profiler;

use ElephantIO\Client;
use Psr\Log\NullLogger;
use ElephantIO\EngineInterface;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionWatcher;
use JKocik\Laravel\Profiler\Contracts\RequestHandledListener;
use JKocik\Laravel\Profiler\Services\BroadcastingEngineService;
use JKocik\Laravel\Profiler\LaravelExecution\LaravelExecutionData;

class LaravelProfiler implements Profiler
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * LaravelProfiler constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

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
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $dataTracker = $this->app->make(DataTracker::class);
        $dataProcessor = $this->app->make(DataProcessor::class);
        $executionWatcher = $this->app->make(ExecutionWatcher::class);

        $executionWatcher->watch();
        $dataTracker->track();
        $this->app->terminating(function () use ($dataTracker, $dataProcessor) {
            $dataTracker->terminate();
            $dataProcessor->process($dataTracker);
        });
    }
}
