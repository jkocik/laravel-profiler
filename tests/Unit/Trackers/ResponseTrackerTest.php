<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ResponseTracker;

class ResponseTrackerTest extends TestCase
{
    /** @test */
    function has_status()
    {
        $tracker = $this->app->make(ResponseTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('status'));
        $this->assertEquals(200, $tracker->meta()->get('status'));
    }
}
