<?php

return [

    /*
    |--------------------------------------------------------------------------
    | General Laravel Profiler enable / disable setting
    |--------------------------------------------------------------------------
    |
    | This value enables / disables Profiler. If set to false all settings
    | below it do not take effect.
    |
    */

    'enabled' => env('PROFILER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Override enabled Profiler by env
    |--------------------------------------------------------------------------
    |
    | Can disable Profiler on particular env. Profiler is disabled by default
    | on production. Take effect only if above (enabled) is set to true.
    |
    */

    'force_disable_on' => [
        'production' => true,
//        'testing' => true,
//        'local' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Profiler trackers
    |--------------------------------------------------------------------------
    |
    | Trackers collect data of framework execution. You can decide what data
    | you want to collect by commenting / uncommenting particular trackers.
    |
    */

    'trackers' => [
//        \JKocik\Laravel\Profiler\Trackers\ConfigTracker::class, // App > Config tab
//        \JKocik\Laravel\Profiler\Trackers\ServiceProvidersTracker::class, // App > Service Providers tab
//        \JKocik\Laravel\Profiler\Trackers\BindingsTracker::class, // App > Bindings tab
//        \JKocik\Laravel\Profiler\Trackers\PathsTracker::class, // App > Paths tab
        \JKocik\Laravel\Profiler\Trackers\SessionTracker::class, // Request > Session tab
        \JKocik\Laravel\Profiler\Trackers\RouteTracker::class, // Request > Route tab
//        \JKocik\Laravel\Profiler\Trackers\ServerTracker::class, // Request > Server tab
//        \JKocik\Laravel\Profiler\Trackers\ContentTracker::class, // Response > Content and JSON tabs
        \JKocik\Laravel\Profiler\Trackers\ViewsTracker::class, // Views tab
        \JKocik\Laravel\Profiler\Trackers\EventsTracker::class, // Events tab
        \JKocik\Laravel\Profiler\Trackers\QueriesTracker::class, // Queries tab
        \JKocik\Laravel\Profiler\Trackers\AuthTracker::class, // Auth tab
        \JKocik\Laravel\Profiler\Trackers\ExceptionTracker::class, // Exception tab
    ],

    /*
    |--------------------------------------------------------------------------
    | Profiler processors
    |--------------------------------------------------------------------------
    |
    | Processors process data collected by trackers. Default processor
    | broadcasts data through Profiler Server to Profiler Client.
    |
    */

    'processors' => [
        \JKocik\Laravel\Profiler\Processors\BroadcastingProcessor::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | BroadcastingProcessor settings
    |--------------------------------------------------------------------------
    |
    | These settings let you set up connection between Profiler Package
    | (this Laravel package) and Profiler Server using HTTP protocol.
    |
    */

    'broadcasting' => [
        'address' => 'http://localhost',
        'port' => '8099',
    ],

    /*
    |--------------------------------------------------------------------------
    | Data passed to tracked items
    |--------------------------------------------------------------------------
    |
    | That can be very heavy when your views or events receive a lot of data.
    | Avoid using it on testing env specially when running whole test suite.
    |
    */

    'data' => [
        'views' => false,
        'events' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Grouping
    |--------------------------------------------------------------------------
    |
    | There can be a lot of the same events fired one by one. Grouping can
    | help you review them on better organized list of events.
    |
    */

    'group' => [
        'events' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Handle Profiler exceptions
    |--------------------------------------------------------------------------
    |
    | Profiler Package (this Laravel package) can throw exceptions when
    | for example BroadcastingProcessor can not connect to Profiler Server.
    | Handle exceptions settings are [int]:
    | 0 - catch exceptions and do not report them
    | 1 - catch exceptions and report them in logs
    | 666 - do not catch exceptions, let them be thrown
    |
    */

    'handle_profiler_exceptions' => 1,

];
