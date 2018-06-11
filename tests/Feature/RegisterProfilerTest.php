<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\ServiceProvider;
use JKocik\Laravel\Profiler\LaravelProfiler;
use JKocik\Laravel\Profiler\DisabledProfiler;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\LaravelListeners\HttpRequestHandledListener;
use JKocik\Laravel\Profiler\LaravelListeners\ConsoleCommandFinishedListener;

class RegisterProfilerTest extends TestCase
{
    /**
     * @param Application $app
     * @param DataTracker $dataTracker
     * @param DataProcessor $dataProcessor
     * @return ServiceProvider
     */
    protected function serviceProvider(
        Application $app,
        DataTracker $dataTracker,
        DataProcessor $dataProcessor
    ): ServiceProvider {
        return new class($app, $dataTracker, $dataProcessor) extends ServiceProvider {
            public function __construct(
                Application $app,
                DataTracker $dataTracker,
                DataProcessor $dataProcessor
            ) {
                parent::__construct($app);
                $this->dataTracker = $dataTracker;
                $this->dataProcessor = $dataProcessor;
            }
            public function register(): void {
                parent::register();
                $this->app->singleton(DataTracker::class, function () {
                    return $this->dataTracker;
                });
                $this->app->singleton(DataProcessor::class, function () {
                    return $this->dataProcessor;
                });
            }
        };
    }

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

    /** @test */
    function enabled_profiler_tracks_laravel()
    {
        $dataTracker = Mockery::spy(DataTracker::class);
        $dataProcessor = Mockery::spy(DataProcessor::class);
        $httpRequestHandledListener = Mockery::spy(HttpRequestHandledListener::class);
        $consoleCommandFinishedListener = Mockery::spy(ConsoleCommandFinishedListener::class);

        $this->app = $this->appWithoutProfiler();
        $this->app->instance(HttpRequestHandledListener::class, $httpRequestHandledListener);
        $this->app->instance(ConsoleCommandFinishedListener::class, $consoleCommandFinishedListener);
        $serviceProvider = $this->serviceProvider($this->app, $dataTracker, $dataProcessor);
        $this->app->register($serviceProvider);

        $this->app->terminate();

        $this->assertInstanceOf(LaravelProfiler::class, $this->app->make(Profiler::class));
        $this->assertSame($dataTracker, $this->app->make(DataTracker::class));
        $dataTracker->shouldHaveReceived('track');
        $dataTracker->shouldHaveReceived('terminate');
        $this->assertSame($dataProcessor, $this->app->make(DataProcessor::class));
        $dataProcessor->shouldHaveReceived('process');
        $httpRequestHandledListener->shouldHaveReceived('listen');
        $consoleCommandFinishedListener->shouldHaveReceived('listen');
    }

    /** @test */
    function disabled_profiler_does_not_track_laravel()
    {
        putenv('PROFILER_ENABLED=false');
        $dataTracker = Mockery::spy(DataTracker::class);
        $dataProcessor = Mockery::spy(DataProcessor::class);
        $httpRequestHandledListener = Mockery::spy(HttpRequestHandledListener::class);
        $consoleCommandFinishedListener = Mockery::spy(ConsoleCommandFinishedListener::class);

        $this->app = $this->appWithoutProfiler();
        $this->app->instance(HttpRequestHandledListener::class, $httpRequestHandledListener);
        $this->app->instance(ConsoleCommandFinishedListener::class, $consoleCommandFinishedListener);
        $serviceProvider = $this->serviceProvider($this->app, $dataTracker, $dataProcessor);
        $this->app->register($serviceProvider);

        $this->app->terminate();

        $this->assertInstanceOf(DisabledProfiler::class, $this->app->make(Profiler::class));
        $this->assertSame($dataTracker, $this->app->make(DataTracker::class));
        $dataTracker->shouldNotHaveReceived('track');
        $dataTracker->shouldNotHaveReceived('terminate');
        $this->assertSame($dataProcessor, $this->app->make(DataProcessor::class));
        $dataProcessor->shouldNotHaveReceived('process');
        $httpRequestHandledListener->shouldNotHaveReceived('listen');
        $consoleCommandFinishedListener->shouldNotHaveReceived('listen');
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();

        putenv('APP_ENV=local');
        putenv('PROFILER_ENABLED');
    }
}
