<?php

use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Services\LogService;
use JKocik\Laravel\Profiler\Services\Performance\TimerException;

if (! function_exists('profiler_start')) {
    function profiler_start(string $name): void
    {
        try {
            app()->make(Timer::class)->startCustom($name);
        } catch (TimerException $e) {
            app()->make(LogService::class)->error($e);
        }
    }
}

if (! function_exists('profiler_finish')) {
    function profiler_finish(string $name): void
    {
        try {
            app()->make(Timer::class)->finishCustom($name);
        } catch (TimerException $e) {
            app()->make(LogService::class)->error($e);
        }
    }
}

if (! function_exists('profiler_reset')) {
    function profiler_reset(): void
    {
        app()->make(Profiler::class)->resetTrackers();
    }
}
