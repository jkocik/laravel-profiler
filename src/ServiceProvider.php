<?php

namespace JKocik\Laravel\Profiler;

use ElephantIO\EngineInterface;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JKocik\Laravel\Profiler\Services\BroadcastingEngineService;

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

        $this->app->bind(EngineInterface::class, BroadcastingEngineService::class);

        $this->app->singleton(Profiler::class, function () {
            return ProfilerResolver::resolve($this->app);
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->allowConfigFileToBePublished();

        $this->app->make(Profiler::class)->boot(
            $this->app->make(DataTracker::class),
            $this->app->make(DataProcessor::class)
        );
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
