<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use Illuminate\Http\Request;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\DataService;
use JKocik\Laravel\Profiler\Trackers\RequestTracker;

class RequestTrackerTest extends TestCase
{
    /** @test */
    function has_method()
    {
        $request = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $request->shouldReceive('method')->andReturn('PUT')->once();
        $tracker = $this->app->make(RequestTracker::class);

        $this->app->make(DataService::class)->setRequest($request);
        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('method'));
        $this->assertEquals('PUT', $tracker->meta()->get('method'));
    }

    /** @test */
    function has_path()
    {
        $request = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $request->shouldReceive('path')->andReturn('path/to/page')->once();
        $tracker = $this->app->make(RequestTracker::class);

        $this->app->make(DataService::class)->setRequest($request);
        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('path'));
        $this->assertEquals('path/to/page', $tracker->meta()->get('path'));
    }

    /** @test */
    function has_is_ajax()
    {
        $request = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $request->shouldReceive('ajax')->andReturn(true)->once();
        $tracker = $this->app->make(RequestTracker::class);

        $this->app->make(DataService::class)->setRequest($request);
        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('is_ajax'));
        $this->assertEquals(true, $tracker->meta()->get('is_ajax'));
    }
}
