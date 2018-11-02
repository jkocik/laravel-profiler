<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;

class CommandsTest extends TestCase
{
    use InteractsWithConsole;

    /** @test */
    function tells_status_if_profiler_is_enabled()
    {
        $this->tapLaravelVersionFrom(5.7, function () {
            $this->artisan('profiler:status')
                ->expectsOutput('Laravel Profiler is enabled')
                ->assertExitCode(0);
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });
    }

    /** @test */
    function tells_status_if_profiler_is_disabled()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.enabled', false);
        });

        $this->tapLaravelVersionFrom(5.7, function () {
            $this->artisan('profiler:status')
                ->expectsOutput('Laravel Profiler is disabled')
                ->assertExitCode(0);
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });
    }

    /**
     * @var array
     */
    protected $beforeApplicationDestroyedCallbacks = [];

    /**
     * @param callable $callback
     */
    protected function beforeApplicationDestroyed(callable $callback)
    {
        $this->beforeApplicationDestroyedCallbacks[] = $callback;
    }
}
