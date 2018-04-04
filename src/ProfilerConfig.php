<?php

namespace JKocik\Laravel\Profiler;

class ProfilerConfig
{
    /**
     * @return string
     */
    public static function broadcastingEvent(): string
    {
        return config('profiler.broadcasting_event');
    }

    /**
     * @param string $key
     * @return array
     */
    public static function trackers(string $key = 'profiler.trackers'): array
    {
        return static::config($key);
    }

    /**
     * @param string $key
     * @return array
     */
    public static function processors(string $key = 'profiler.processors'): array
    {
        return static::config($key);
    }

    /**
     * @param string $key
     * @return array
     */
    protected static function config(string $key): array
    {
        return config($key, []);
    }
}
