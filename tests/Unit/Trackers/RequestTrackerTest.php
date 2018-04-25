<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\RequestTracker;

class RequestTrackerTest extends TestCase
{
    /** @test */
    function has_method()
    {
        $tracker = $this->app->make(RequestTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('method'));
        $this->assertEquals('GET', $tracker->meta()->get('method'));
    }

    /** @test */
    function has_path()
    {
        $tracker = $this->app->make(RequestTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('path'));
        $this->assertEquals('/', $tracker->meta()->get('path'));
    }

    /** @test */
    function has_is_ajax()
    {
        $tracker = $this->app->make(RequestTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('is_ajax'));
        $this->assertEquals(false, $tracker->meta()->get('is_ajax'));
    }
}
