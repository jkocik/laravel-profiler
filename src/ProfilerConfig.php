<?php

namespace JKocik\Laravel\Profiler;

class ProfilerConfig
{
    /**
     * @return array
     */
    public static function trackers(): array
    {
        return config('profiler.trackers', []);
    }

    /**
     * @return array
     */
    public static function processors(): array
    {
        return config('profiler.processors', []);
    }
}
