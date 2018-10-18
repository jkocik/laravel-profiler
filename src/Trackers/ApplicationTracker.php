<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Services\GeneratorService;

class ApplicationTracker extends BaseTracker
{
    /**
     * @var GeneratorService
     */
    protected $generatorService;

    /**
     * ApplicationTracker constructor.
     * @param Application $app
     * @param GeneratorService $generatorService
     */
    public function __construct(Application $app, GeneratorService $generatorService)
    {
        parent::__construct($app);

        $this->generatorService = $generatorService;
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->meta->put('execution_at', time());
        $this->meta->put('id', $this->generatorService->unique32CharsId());
        $this->meta->put('laravel_version', $this->app->version());
        $this->meta->put('php_version', phpversion());
        $this->meta->put('env', $this->app->environment());
        $this->meta->put('is_running_in_console', $this->app->runningInConsole());

        $this->data->put('application', Collection::make([
            'locale' => $this->app->getLocale(),
            'configuration_is_cached' => $this->app->configurationIsCached(),
            'routes_are_cached' => $this->app->routesAreCached(),
            'is_down_for_maintenance' => $this->app->isDownForMaintenance(),
            'should_skip_middleware' => $this->app->shouldSkipMiddleware(),
        ]));
    }
}
