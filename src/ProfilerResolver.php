<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Services\ConfigService;

class ProfilerResolver
{
    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * ProfilerResolver constructor.
     * @param ConfigService $configService
     */
    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @return Profiler
     */
    public function resolve(): Profiler
    {
        if (! $this->configService->isProfilerEnabled()) {
            return new DisabledProfiler();
        }

        return new LaravelProfiler();
    }
}
