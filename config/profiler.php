<?php

return [
    'enabled' => env('PROFILER_ENABLED', true),
    'force_disable_on' => [
        'production' => true,
        'testing' => false,
        'local' => false,
    ],
    'trackers' => [
        \JKocik\Laravel\Profiler\Trackers\PathsTracker::class,
        \JKocik\Laravel\Profiler\Trackers\RouteTracker::class,
        \JKocik\Laravel\Profiler\Trackers\ViewsTracker::class,
        \JKocik\Laravel\Profiler\Trackers\ConfigTracker::class,
        \JKocik\Laravel\Profiler\Trackers\EventsTracker::class,
        \JKocik\Laravel\Profiler\Trackers\QueriesTracker::class,
        \JKocik\Laravel\Profiler\Trackers\SessionTracker::class,
        \JKocik\Laravel\Profiler\Trackers\BindingsTracker::class,
        \JKocik\Laravel\Profiler\Trackers\ServiceProvidersTracker::class,
    ],
    'processors' => [
        \JKocik\Laravel\Profiler\Processors\BroadcastingProcessor::class,
    ],
    'broadcasting' => [
        'address' => 'http://localhost',
        'port' => '8099',
        'log_errors_enabled' => true,
    ],
];
