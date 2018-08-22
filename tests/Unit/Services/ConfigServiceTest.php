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
    function returns_laravel_profiler_broadcasting_url()
    {
        $url = $this->app->make(ConfigService::class)->broadcastingUrl();

        $this->assertEquals('http://localhost:8099', $url);
    }

    /** @test */
    function returns_laravel_profiler_broadcasting_log_errors_enabled()
    {
        $appA = $this->app;
        $appB = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.broadcasting.log_errors_enabled', false);
        });

        $logErrorsA = $appA->make(ConfigService::class)->broadcastingLogErrorsEnabled();
        $logErrorsB = $appB->make(ConfigService::class)->broadcastingLogErrorsEnabled();

        $this->assertTrue($logErrorsA);
        $this->assertFalse($logErrorsB);
    }
}
