<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\ServiceProvider;

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
}
