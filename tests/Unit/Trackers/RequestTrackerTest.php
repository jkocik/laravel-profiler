<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\RequestTracker;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;

class RequestTrackerTest extends TestCase
{
    /** @test */
    function has_request_meta()
    {
        $request = Mockery::mock(ExecutionRequest::class);
        $request->shouldReceive('meta')->andReturn(collect(['type' => 'a', 'method' => 'b', 'path' => 'c']));
        $this->app->make(ExecutionData::class)->setRequest($request);

        $tracker = $this->app->make(RequestTracker::class);
        $tracker->terminate();

        $this->assertEquals('a', $tracker->meta()->get('type'));
        $this->assertEquals('b', $tracker->meta()->get('method'));
        $this->assertEquals('c', $tracker->meta()->get('path'));
    }
}
