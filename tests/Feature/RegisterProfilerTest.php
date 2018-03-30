<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\ServiceProvider;
use JKocik\Laravel\Profiler\LaravelProfiler;
use JKocik\Laravel\Profiler\DisabledProfiler;
use JKocik\Laravel\Profiler\Contracts\Profiler;

class RegisterProfilerTest extends TestCase
{
    /** @test */
    function loads_profiler_config_file()
    {
        $config = $this->app->make('config');

        $this->assertTrue($config->has('profiler'));
    }

    /** @test */
    function allows_config_file_to_be_published()
    {
        $this->assertArrayHasKey(
            ServiceProvider::profilerConfigPath(),
            ServiceProvider::pathsToPublish()
        );
    }

    /** @test */
    function profiler_is_enabled_by_default()
    {
        putenv('APP_ENV=test-profiler');
        $this->app = $this->app();

        $config = $this->app->make('config');

        $this->assertTrue($config->get('profiler.enabled'));
        $this->assertInstanceOf(LaravelProfiler::class, $this->app->make(Profiler::class));
    }

    /** @test */
    function profiler_can_be_disabled_in_env_file()
    {
        putenv('APP_ENV=test-profiler');
        putenv('PROFILER_ENABLED=false');
        $this->app = $this->app();

        $config = $this->app->make('config');

        $this->assertFalse($config->get('profiler.enabled'));
        $this->assertInstanceOf(DisabledProfiler::class, $this->app->make(Profiler::class));
    }

    /** @test */
    function profiler_can_be_disabled_in_config_file_for_specific_environment()
    {
        putenv('APP_ENV=test-profiler');
        putenv('PROFILER_ENABLED=true');
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.force_disable_on', ['test-profiler']);
        });

        $this->assertInstanceOf(DisabledProfiler::class, $this->app->make(Profiler::class));
    }

    /** @test */
    function profiler_is_forced_by_default_to_be_disabled_on_production()
    {
        putenv('APP_ENV=production');
        putenv('PROFILER_ENABLED=true');
        $this->app = $this->app();

        $config = $this->app->make('config');

        $this->assertContains('production', $config->get('profiler.force_disable_on'));
        $this->assertInstanceOf(DisabledProfiler::class, $this->app->make(Profiler::class));
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        putenv('APP_ENV=local');
        putenv('PROFILER_ENABLED');
    }
}
