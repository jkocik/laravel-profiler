<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Profiler;

class ProfilerResolver
{
    /**
     * @param Application $app
     * @return Profiler
     */
    public static function resolve(Application $app): Profiler
    {
        if (static::isProfilerDisabled($app)) {
            return new DisabledProfiler();
        }

        return new LaravelProfiler();
    }

    /**
     * @param Application $app
     * @return bool
     */
    protected static function isProfilerDisabled(Application $app): bool
    {
        $config = $app->make('config');

        if ($app->environment($config->get('profiler.force_disable_on'))) {
            return true;
        }

        return $config->get('profiler.enabled') !== true;
    }
}
