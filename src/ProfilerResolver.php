<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Services\ConfigService;

class ProfilerResolver
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * ProfilerResolver constructor.
     * @param Application $app
     * @param ConfigService $configService
     */
    public function __construct(Application $app, ConfigService $configService)
    {
        $this->app = $app;
        $this->configService = $configService;
    }

    /**
     * @return Profiler
     */
    public function resolve(): Profiler
    {
        if (! $this->configService->isProfilerEnabled()) {
            return $this->app->make(DisabledProfiler::class);
        }

        return $this->app->make(LaravelProfiler::class);
    }
}
