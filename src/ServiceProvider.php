<?php

namespace JKocik\Laravel\Profiler;

use ElephantIO\EngineInterface;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Contracts\DataService;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\Services\LaravelDataService;
use JKocik\Laravel\Profiler\Http\HttpKernelHandledListener;
use JKocik\Laravel\Profiler\Contracts\RequestHandledListener;
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

        $this->app->singleton(DataService::class, function () {
            return $this->app->make(LaravelDataService::class);
        });

        $this->app->singleton(RequestHandledListener::class, function () {
            return $this->app->make(HttpKernelHandledListener::class);
        });

        $this->app->singleton(Profiler::class, function () {
            return $this->app->make(ProfilerResolver::class)->resolve();
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->allowConfigFileToBePublished();

        $this->app->make(Profiler::class)->boot(
            $this->app,
            $this->app->make(DataTracker::class),
            $this->app->make(DataProcessor::class),
            $this->app->make(RequestHandledListener::class)
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
