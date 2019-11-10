<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\AuthTracker;
use JKocik\Laravel\Profiler\Trackers\RedisTracker;
use JKocik\Laravel\Profiler\Trackers\PathsTracker;
use JKocik\Laravel\Profiler\Trackers\RouteTracker;
use JKocik\Laravel\Profiler\Trackers\ViewsTracker;
use JKocik\Laravel\Profiler\Trackers\EventsTracker;
use JKocik\Laravel\Profiler\Trackers\ConfigTracker;
use JKocik\Laravel\Profiler\Trackers\ServerTracker;
use JKocik\Laravel\Profiler\Trackers\ContentTracker;
use JKocik\Laravel\Profiler\Trackers\QueriesTracker;
use JKocik\Laravel\Profiler\Trackers\SessionTracker;
use JKocik\Laravel\Profiler\Trackers\RequestTracker;
use JKocik\Laravel\Profiler\Trackers\ResponseTracker;
use JKocik\Laravel\Profiler\Trackers\BindingsTracker;
use JKocik\Laravel\Profiler\Trackers\ExceptionTracker;
use JKocik\Laravel\Profiler\Trackers\ApplicationTracker;
use JKocik\Laravel\Profiler\Trackers\PerformanceTracker;
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
    function performance_tracker_is_required()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $performanceTracker = Mockery::mock(PerformanceTracker::class);
        $performanceTracker->shouldReceive('terminate')->once();
        $performanceTracker->shouldReceive('meta')->once()->andReturn(collect());
        $performanceTracker->shouldReceive('data')->once()->andReturn(collect());

        $this->app = $this->appWith(function (Application $app) use ($performanceTracker) {
            $app->make('config')->set('profiler.trackers', []);
            $app->make('config')->set('profiler.processors', []);
            $app->singleton(PerformanceTracker::class, function () use ($performanceTracker) {
                return $performanceTracker;
            });
        });

        $this->app->terminate();

        $this->assertNotContains(PerformanceTracker::class, $defaultTrackers);
        $this->assertSame($performanceTracker, $this->app->make(PerformanceTracker::class));
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

    /** @test */
    function config_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(ConfigTracker::class, $defaultTrackers);
    }

    /** @test */
    function session_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(SessionTracker::class, $defaultTrackers);
    }

    /** @test */
    function route_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(RouteTracker::class, $defaultTrackers);
    }

    /** @test */
    function views_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(ViewsTracker::class, $defaultTrackers);
    }

    /** @test */
    function events_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(EventsTracker::class, $defaultTrackers);
    }

    /** @test */
    function queries_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(QueriesTracker::class, $defaultTrackers);
    }

    /** @test */
    function server_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(ServerTracker::class, $defaultTrackers);
    }

    /** @test */
    function content_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(ContentTracker::class, $defaultTrackers);
    }

    /** @test */
    function auth_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(AuthTracker::class, $defaultTrackers);
    }

    /** @test */
    function exception_tracker_is_enabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertContains(ExceptionTracker::class, $defaultTrackers);
    }

    /** @test */
    function redis_tracker_is_disabled_by_default()
    {
        $defaultTrackers = $this->app->make('config')->get('profiler.trackers');

        $this->assertNotContains(RedisTracker::class, $defaultTrackers);
    }
}
