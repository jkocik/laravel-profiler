<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\PathsTracker;
use JKocik\Laravel\Profiler\Trackers\RequestTracker;
use JKocik\Laravel\Profiler\Trackers\ResponseTracker;
use JKocik\Laravel\Profiler\Trackers\BindingsTracker;
use JKocik\Laravel\Profiler\Trackers\ApplicationTracker;
use JKocik\Laravel\Profiler\Trackers\ServiceProvidersTracker;

class TrackersTest extends TestCase
{
    /** @test */
    function application_tracker_is_required()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $applicationTracker = Mockery::mock(ApplicationTracker::class);
        $applicationTracker->shouldReceive('terminate')->once();
        $applicationTracker->shouldReceive('meta')->once()->andReturn(collect());
        $applicationTracker->shouldReceive('data')->once()->andReturn(collect());

        $this->app = $this->appWith(function (Application $app) use ($applicationTracker) {
            $app->make('config')->set('profiler.trackers', []);
            $app->make('config')->set('profiler.processors', []);
            $app->singleton(ApplicationTracker::class, function () use ($applicationTracker) {
                return $applicationTracker;
            });
        });

        $this->app->terminate();

        $this->assertNotContains(ApplicationTracker::class, $defaultTrackers);
        $this->assertSame($applicationTracker, $this->app->make(ApplicationTracker::class));
    }

    /** @test */
    function request_tracker_is_required()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $requestTracker = Mockery::mock(RequestTracker::class);
        $requestTracker->shouldReceive('terminate')->once();
        $requestTracker->shouldReceive('meta')->once()->andReturn(collect());
        $requestTracker->shouldReceive('data')->once()->andReturn(collect());

        $this->app = $this->appWith(function (Application $app) use ($requestTracker) {
            $app->make('config')->set('profiler.trackers', []);
            $app->make('config')->set('profiler.processors', []);
            $app->singleton(RequestTracker::class, function () use ($requestTracker) {
                return $requestTracker;
            });
        });

        $this->app->terminate();

        $this->assertNotContains(RequestTracker::class, $defaultTrackers);
        $this->assertSame($requestTracker, $this->app->make(RequestTracker::class));
    }

    /** @test */
    function response_tracker_is_required()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $responseTracker = Mockery::mock(ResponseTracker::class);
        $responseTracker->shouldReceive('terminate')->once();
        $responseTracker->shouldReceive('meta')->once()->andReturn(collect());
        $responseTracker->shouldReceive('data')->once()->andReturn(collect());

        $this->app = $this->appWith(function (Application $app) use ($responseTracker) {
            $app->make('config')->set('profiler.trackers', []);
            $app->make('config')->set('profiler.processors', []);
            $app->singleton(ResponseTracker::class, function () use ($responseTracker) {
                return $responseTracker;
            });
        });

        $this->app->terminate();

        $this->assertNotContains(ResponseTracker::class, $defaultTrackers);
        $this->assertSame($responseTracker, $this->app->make(ResponseTracker::class));
    }

    /** @test */
    function paths_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(PathsTracker::class, $defaultTrackers);
    }

    /** @test */
    function service_providers_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(ServiceProvidersTracker::class, $defaultTrackers);
    }

    /** @test */
    function bindings_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(BindingsTracker::class, $defaultTrackers);
    }
}
