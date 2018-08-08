<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\Profiler;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(static::profilerConfigPath(), 'profiler');

        $this->app->singleton(Profiler::class, function ($app) {
            return $app->make(ProfilerResolver::class)->resolve();
        });

        $this->app->make(Profiler::class)->register();
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
