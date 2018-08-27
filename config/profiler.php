<?php

return [
    'enabled' => env('PROFILER_ENABLED', true),
    'force_disable_on' => [
        'production' => true,
        'testing' => false,
        'local' => false,
    ],
    'trackers' => [
        \JKocik\Laravel\Profiler\Trackers\ConfigTracker::class, // App > Config tab
        \JKocik\Laravel\Profiler\Trackers\ServiceProvidersTracker::class, // App > Service Providers tab
        \JKocik\Laravel\Profiler\Trackers\BindingsTracker::class, // App > Bindings tab
        \JKocik\Laravel\Profiler\Trackers\PathsTracker::class, // App > Paths tab
        \JKocik\Laravel\Profiler\Trackers\SessionTracker::class, // Request > Session tab
        \JKocik\Laravel\Profiler\Trackers\RouteTracker::class, // Request > Route tab
        \JKocik\Laravel\Profiler\Trackers\ServerTracker::class, // Request > Server tab
        \JKocik\Laravel\Profiler\Trackers\ContentTracker::class, // Response > Content and JSON tabs
        \JKocik\Laravel\Profiler\Trackers\ViewsTracker::class, // Views tab
        \JKocik\Laravel\Profiler\Trackers\EventsTracker::class, // Events tab
        \JKocik\Laravel\Profiler\Trackers\QueriesTracker::class, // Queries tab
    ],
    'processors' => [
        \JKocik\Laravel\Profiler\Processors\BroadcastingProcessor::class,
    ],
    'broadcasting' => [
        'address' => 'http://localhost',
        'port' => '8099',
        'log_errors_enabled' => true,
    ],
    'data' => [
        'views' => false,
        'events' => false,
    ],
    'group' => [
        'events' => true,
    ],
];
