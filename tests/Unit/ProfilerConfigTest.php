<?php

namespace JKocik\Laravel\Profiler\Tests\Unit;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\ProfilerConfig;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorB;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerB;

class ProfilerConfigTest extends TestCase
{
    /** @test */
    function returns_trackers()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.trackers', [
                TrackerA::class,
                TrackerB::class,
            ]);
        });

        $trackers = ProfilerConfig::trackers();

        $this->assertContains(TrackerA::class, $trackers);
        $this->assertContains(TrackerB::class, $trackers);
    }

    /** @test */
    function returns_processors()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.processors', [
                ProcessorA::class,
                ProcessorB::class,
            ]);
        });

        $processors = ProfilerConfig::processors();

        $this->assertContains(ProcessorA::class, $processors);
        $this->assertContains(ProcessorB::class, $processors);
    }

    /** @test */
    function returns_laravel_profiler_broadcasting_event_name()
    {
        $event = ProfilerConfig::broadcastingEvent();

        $this->assertEquals('laravel-profiler-broadcasting', $event);
    }

    /** @test */
    function returns_laravel_profiler_broadcasting_url()
    {
        $url = ProfilerConfig::broadcastingUrl();

        $this->assertEquals('http://10.0.2.2:61976', $url);
    }
}
