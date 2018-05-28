<?php

namespace JKocik\Laravel\Profiler;

use ElephantIO\Client;
use Psr\Log\NullLogger;
use ElephantIO\EngineInterface;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionWatcher;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JKocik\Laravel\Profiler\Services\BroadcastingEngineService;
use JKocik\Laravel\Profiler\LaravelExecution\LaravelExecutionData;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(static::profilerConfigPath(), 'profiler');

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

        $this->app->singleton(Profiler::class, function ($app) {
            return $app->make(ProfilerResolver::class)->resolve();
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->allowConfigFileToBePublished();

        $this->app->make(Profiler::class)->boot();
    }

    /**
     * @return void
     */
    public function allowConfigFileToBePublished(): void
    {
        $this->publishes([
            static::profilerConfigPath() => config_path('profiler.php'),
        ]);
    }

    /**
     * @return string
     */
    public static function profilerConfigPath(): string
    {
        return __DIR__ . '/../config/profiler.php';
    }
}
