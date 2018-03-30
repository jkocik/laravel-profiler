<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(static::profilerConfigPath(), 'profiler');
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->allowConfigFileToBePublished();
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
