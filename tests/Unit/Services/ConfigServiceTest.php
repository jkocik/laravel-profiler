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

        $this->assertTrue($trackers->contains(TrackerA::class));
        $this->assertTrue($trackers->contains(TrackerB::class));
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

        $this->assertTrue($processors->contains(ProcessorA::class));
        $this->assertTrue($processors->contains(ProcessorB::class));
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

    /** @test */
    function returns_laravel_profiler_broadcasting_log_errors()
    {
        $appA = $this->app;
        $appB = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.broadcasting_log_errors', false);
        });

        $logErrorsA = $appA->make(ConfigService::class)->broadcastingLogErrors();
        $logErrorsB = $appB->make(ConfigService::class)->broadcastingLogErrors();

        $this->assertTrue($logErrorsA);
        $this->assertFalse($logErrorsB);
    }
}
