<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Tests\Support\PHPMock;
use JKocik\Laravel\Profiler\Trackers\ApplicationTracker;

class ApplicationTrackerTest extends TestCase
{
    /** @test */
    function has_execution_at()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('execution_at'));
        $this->assertEquals(PHPMock::TIME, $tracker->meta()->get('execution_at'));
    }

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
    function has_laravel_version()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('laravel_version'));
        $this->assertEquals($this->app->version(), $tracker->meta()->get('laravel_version'));
    }

    /** @test */
    function has_php_version()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('php_version'));
        $this->assertEquals(PHPMock::PHP_VERSION, $tracker->meta()->get('php_version'));
    }

    /** @test */
    function has_env()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('env'));
        $this->assertEquals('local', $tracker->meta()->get('env'));
    }

    /** @test */
    function has_is_running_in_console()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('is_running_in_console'));
        $this->assertEquals($this->app->runningInConsole(), $tracker->meta()->get('is_running_in_console'));
    }

    /** @test */
    function has_memory_usage()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('memory_usage'));
        $this->assertEquals(PHPMock::MEMORY_USAGE, $tracker->meta()->get('memory_usage'));
    }
}
