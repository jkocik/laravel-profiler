<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Services\ConsoleService;
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

    /** @test */
    function can_start_profiler_server()
    {
        $consoleService = Mockery::spy(ConsoleService::class);
        $this->app->instance(ConsoleService::class, $consoleService);

        $this->artisan('profiler:server');

        $consoleService->shouldHaveReceived('profilerServerCmd')->once();
    }

    /** @test */
    function can_start_profiler_server_with_correct_command()
    {
        $configService = Mockery::mock(ConfigService::class);
        $configService->shouldReceive('serverHttpPort')->once()->andReturn('1234');
        $configService->shouldReceive('serverSocketsPort')->once()->andReturn('9876');
        $this->app->instance(ConfigService::class, $configService);

        $consoleService = $this->app->make(ConsoleService::class);

        $this->assertEquals(
            'node node_modules/laravel-profiler-client/server/server.js http=1234 ws=9876',
            $consoleService->profilerServerCmd()
        );
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
