<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Services;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerB;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorB;

class ConfigServiceTest extends TestCase
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

        $trackers = $this->app->make(ConfigService::class)->trackers();

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

        $processors = $this->app->make(ConfigService::class)->processors();

        $this->assertContains(ProcessorA::class, $processors);
        $this->assertContains(ProcessorB::class, $processors);
    }

    /** @test */
    function returns_laravel_profiler_broadcasting_event_name()
    {
        $event = $this->app->make(ConfigService::class)->broadcastingEvent();

        $this->assertEquals('laravel-profiler-broadcasting', $event);
    }

    /** @test */
    function returns_laravel_profiler_broadcasting_url()
    {
        $url = $this->app->make(ConfigService::class)->broadcastingUrl();

        $this->assertEquals('http://10.0.2.2:1902', $url);
    }
}
