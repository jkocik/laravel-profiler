<?php

namespace JKocik\Laravel\Profiler\Services;

class ConfigService
{
    /**
     * @return array
     */
    public function trackers(): array
    {
        return config('profiler.trackers');
    }

    /**
     * @return array
     */
    public function processors(): array
    {
        return config('profiler.processors');
    }

    /**
     * @return string
     */
    public function broadcastingEvent(): string
    {
        return config('profiler.broadcasting_event');
    }

    /**
     * @return string
     */
    public function broadcastingUrl(): string
    {
        return config('profiler.broadcasting_address') . ':' . config('profiler.broadcasting_port');
    }
}
