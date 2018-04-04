<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ApplicationTracker;

class TrackersTest extends TestCase
{
    /** @test */
    function application_tracker_is_required()
    {
        $defaultTrackers = config('profiler.trackers');

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
}
