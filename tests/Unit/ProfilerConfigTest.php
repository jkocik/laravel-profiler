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
    function returns_empty_array_when_trackers_configuration_is_empty()
    {
        $trackers = ProfilerConfig::trackers('profiler.trackers-666');

        $this->assertCount(0, $trackers);
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
    function returns_empty_array_when_processors_configuration_is_empty()
    {
        $processors = ProfilerConfig::processors('profiler.processors-666');

        $this->assertCount(0, $processors);
    }

    /** @test */
    function returns_laravel_profiler_broadcasting_event_name()
    {
        $event = ProfilerConfig::broadcastingEvent();

        $this->assertEquals('laravel-profiler-broadcasting', $event);
    }
}
