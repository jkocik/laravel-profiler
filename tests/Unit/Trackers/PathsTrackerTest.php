<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\PathsTracker;

class PathsTrackerTest extends TestCase
{
    /**
     * @param string $method
     * @return bool
     */
    protected function appMissingMethod(string $method): bool
    {
        if (! method_exists($this->app, $method)) {
            $this->assertTrue(true);
            return true;
        }

        return false;
    }

    /**
     * @param string $method
     * @return bool
     */
    protected function appHasMethod(string $method): bool
    {
        if (method_exists($this->app, $method)) {
            $this->assertTrue(true);
            return true;
        }

        return false;
    }

    /** @test */
    function has_app_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('app_path'));
        $this->assertEquals($this->app->path(), $paths->get('app_path'));
    }

    /** @test */
    function has_base_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('base_path'));
        $this->assertEquals($this->app->basePath(), $paths->get('base_path'));
    }

    /** @test */
    function has_lang_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('lang_path'));
        $this->assertEquals($this->app->langPath(), $paths->get('lang_path'));
    }

    /** @test */
    function has_config_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('config_path'));
        $this->assertEquals($this->app->configPath(), $paths->get('config_path'));
    }

    /** @test */
    function has_public_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('public_path'));
        $this->assertEquals($this->app->publicPath(), $paths->get('public_path'));
    }

    /** @test */
    function has_storage_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('storage_path'));
        $this->assertEquals($this->app->storagePath(), $paths->get('storage_path'));
    }

    /** @test */
    function has_database_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('database_path'));
        $this->assertEquals($this->app->databasePath(), $paths->get('database_path'));
    }

    /** @test */
    function has_resource_path()
    {
        if ($this->appMissingMethod('resourcePath')) {
            return;
        }

        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('resource_path'));
        $this->assertEquals($this->app->resourcePath(), $paths->get('resource_path'));
    }

    /** @test */
    function missing_resource_path()
    {
        if ($this->appHasMethod('resourcePath')) {
            return;
        }

        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertFalse($paths->has('resource_path'));
    }

    /** @test */
    function has_bootstrap_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('bootstrap_path'));
        $this->assertEquals($this->app->bootstrapPath(), $paths->get('bootstrap_path'));
    }

    /** @test */
    function has_cached_config_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('cached_config_path'));
        $this->assertEquals($this->app->getCachedConfigPath(), $paths->get('cached_config_path'));
    }

    /** @test */
    function has_cached_routes_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('cached_routes_path'));
        $this->assertEquals($this->app->getCachedRoutesPath(), $paths->get('cached_routes_path'));
    }

    /** @test */
    function has_cached_services_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('cached_services_path'));
        $this->assertEquals($this->app->getCachedServicesPath(), $paths->get('cached_services_path'));
    }

    /** @test */
    function has_cached_packages_path()
    {
        if ($this->appMissingMethod('getCachedPackagesPath')) {
            return;
        }

        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('cached_packages_path'));
        $this->assertEquals($this->app->getCachedPackagesPath(), $paths->get('cached_packages_path'));
    }

    /** @test */
    function missing_cached_packages_path()
    {
        if ($this->appHasMethod('getCachedPackagesPath')) {
            return;
        }

        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertFalse($paths->has('cached_packages_path'));
    }

    /** @test */
    function has_environment_file_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertTrue($paths->has('environment_file_path'));
        $this->assertEquals($this->app->environmentFilePath(), $paths->get('environment_file_path'));
    }
}
