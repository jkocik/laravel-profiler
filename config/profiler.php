<?php

return [
    'enabled' => env('PROFILER_ENABLED', true),
    'force_disable_on' => [
        'production',
//        'testing',
//        'local',
    ],
    'processors' => [
        \JKocik\Laravel\Profiler\Processors\BroadcastingProcessor::class,
    ],
    'broadcasting_event' => 'laravel-profiler-broadcasting',
];
