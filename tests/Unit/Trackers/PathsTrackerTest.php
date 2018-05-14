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

        $this->assertEquals($this->app->path(), $paths->where('name', 'app_path')->first()['path']);
        $this->assertSame(0, $paths->keys()[0]);
    }

    /** @test */
    function has_base_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->basePath(), $paths->where('name', 'base_path')->first()['path']);
    }

    /** @test */
    function has_lang_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->langPath(), $paths->where('name', 'lang_path')->first()['path']);
    }

    /** @test */
    function has_config_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->configPath(), $paths->where('name', 'config_path')->first()['path']);
    }

    /** @test */
    function has_public_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->publicPath(), $paths->where('name', 'public_path')->first()['path']);
    }

    /** @test */
    function has_storage_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->storagePath(), $paths->where('name', 'storage_path')->first()['path']);
    }

    /** @test */
    function has_database_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->databasePath(), $paths->where('name', 'database_path')->first()['path']);
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

        $this->assertEquals($this->app->resourcePath(), $paths->where('name', 'resource_path')->first()['path']);
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

        $this->assertEquals($this->app->bootstrapPath(), $paths->where('name', 'bootstrap_path')->first()['path']);
    }

    /** @test */
    function has_cached_config_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->getCachedConfigPath(), $paths->where('name', 'cached_config_path')->first()['path']);
    }

    /** @test */
    function has_cached_routes_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->getCachedRoutesPath(), $paths->where('name', 'cached_routes_path')->first()['path']);
    }

    /** @test */
    function has_cached_services_path()
    {
        $tracker = $this->app->make(PathsTracker::class);

        $tracker->terminate();
        $paths = $tracker->data()->get('paths');

        $this->assertEquals($this->app->getCachedServicesPath(), $paths->where('name', 'cached_services_path')->first()['path']);
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

        $this->assertEquals($this->app->getCachedPackagesPath(), $paths->where('name', 'cached_packages_path')->first()['path']);
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

        $this->assertEquals($this->app->environmentFilePath(), $paths->where('name', 'environment_file_path')->first()['path']);
    }
}
