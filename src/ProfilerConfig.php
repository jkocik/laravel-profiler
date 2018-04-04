<?php

namespace JKocik\Laravel\Profiler;

class ProfilerConfig
{
    /**
     * @return array
     */
    public static function trackers(): array
    {
        return config('profiler.trackers');
    }

    /**
     * @return array
     */
    public static function processors(): array
    {
        return config('profiler.processors');
    }

    /**
     * @return string
     */
    public static function broadcastingEvent(): string
    {
        return config('profiler.broadcasting_event');
    }

    /**
     * @return string
     */
    public static function broadcastingUrl(): string
    {
        return config('profiler.broadcasting_address') . ':' . config('profiler.broadcasting_port');
    }
}
