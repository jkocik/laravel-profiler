<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use Illuminate\Foundation\Application;
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
        $app = Mockery::mock(Application::class)->shouldIgnoreMissing();
        $app->shouldReceive('runningInConsole')->once()->andReturn(false);
        $this->app->instance(Application::class, $app);

        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('is_running_in_console'));
        $this->assertFalse($tracker->meta()->get('is_running_in_console'));
    }

    /** @test */
    function has_locale()
    {
        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();
        $application = $tracker->data()->get('application');

        $this->assertTrue($application->has('locale'));
        $this->assertEquals($this->app->getLocale(), $application->get('locale'));
    }

    /** @test */
    function has_configuration_is_cached()
    {
        $app = Mockery::mock(Application::class)->shouldIgnoreMissing();
        $app->shouldReceive('configurationIsCached')->once()->andReturn(true);
        $this->app->instance(Application::class, $app);

        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();
        $application = $tracker->data()->get('application');

        $this->assertTrue($application->has('configuration_is_cached'));
        $this->assertTrue($application->get('configuration_is_cached'));
    }

    /** @test */
    function has_routes_are_cached()
    {
        $app = Mockery::mock(Application::class)->shouldIgnoreMissing();
        $app->shouldReceive('routesAreCached')->once()->andReturn(true);
        $this->app->instance(Application::class, $app);

        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();
        $application = $tracker->data()->get('application');

        $this->assertTrue($application->has('routes_are_cached'));
        $this->assertTrue($application->get('routes_are_cached'));
    }

    /** @test */
    function has_is_down_for_maintenance()
    {
        $app = Mockery::mock(Application::class)->shouldIgnoreMissing();
        $app->shouldReceive('isDownForMaintenance')->once()->andReturn(true);
        $this->app->instance(Application::class, $app);

        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();
        $application = $tracker->data()->get('application');

        $this->assertTrue($application->has('is_down_for_maintenance'));
        $this->assertTrue($application->get('is_down_for_maintenance'));
    }

    /** @test */
    function has_should_skip_middleware()
    {
        $app = Mockery::mock(Application::class)->shouldIgnoreMissing();
        $app->shouldReceive('shouldSkipMiddleware')->once()->andReturn(true);
        $this->app->instance(Application::class, $app);

        $tracker = $this->app->make(ApplicationTracker::class);

        $tracker->terminate();
        $application = $tracker->data()->get('application');

        $this->assertTrue($application->has('should_skip_middleware'));
        $this->assertTrue($application->get('should_skip_middleware'));
    }
}
