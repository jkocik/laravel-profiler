<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ApplicationTracker;

class ApplicationTrackerTest extends TestCase
{
    /** @test */
    function has_profiler_single_execution_id()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('id'));
        $this->assertRegExp('/^[a-z0-9]{32}$/', $tracker->meta()->get('id'));
    }

    /** @test */
    function profiler_single_execution_id_is_unique()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();
        $firstId = $tracker->meta()->get('id');

        $tracker->terminate();
        $secondId = $tracker->meta()->get('id');

        $this->assertNotEquals($firstId, $secondId);
    }

    /** @test */
    function has_version()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('version'));
        $this->assertEquals($this->app->version(), $tracker->meta()->get('version'));
    }

    /** @test */
    function has_env()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('env'));
        $this->assertEquals('local', $tracker->meta()->get('env'));
    }
}
