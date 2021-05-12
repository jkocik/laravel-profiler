<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\ServiceProvider;
use JKocik\Laravel\Profiler\LaravelProfiler;
use JKocik\Laravel\Profiler\DisabledProfiler;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Events\Terminating;
use JKocik\Laravel\Profiler\Events\ProfilerBound;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use JKocik\Laravel\Profiler\Services\Performance\TimerService;
use JKocik\Laravel\Profiler\Services\Performance\NullTimerService;
use JKocik\Laravel\Profiler\LaravelListeners\HttpRequestHandledListener;
use JKocik\Laravel\Profiler\LaravelListeners\ConsoleCommandFinishedListener;

class RegisterProfilerTest extends TestCase
{
    /**
     * @param Application $app
     * @param Timer $timer
     * @param DataTracker $dataTracker
     * @param DataProcessor $dataProcessor
     * @return void
     */
    protected function registerServiceProviderWith(
        Application $app,
        Timer $timer,
        DataTracker $dataTracker,
        DataProcessor $dataProcessor
    ): void {
        $provider = new class($app, $timer, $dataTracker, $dataProcessor) extends ServiceProvider {
            public function __construct(
                Application $app,
                Timer $timer,
                DataTracker $dataTracker,
                DataProcessor $dataProcessor
            ) {
                parent::__construct($app);
                $this->timer = $timer;
                $this->dataTracker = $dataTracker;
                $this->dataProcessor = $dataProcessor;
            }
            public function register(): void {
                Event::listen(ProfilerBound::class, function () {
                    $this->app->singleton(Timer::class, function () {
                        return $this->timer;
                    });
                    $this->app->singleton(DataTracker::class, function () {
                        return $this->dataTracker;
                    });
                    $this->app->singleton(DataProcessor::class, function () {
                        return $this->dataProcessor;
                    });
                });
                parent::register();
            }
        };

        $app->afterBootstrapping(RegisterProviders::class, function () use ($app, $provider) {
            $app->register($provider);
        });

        $app->make(Kernel::class)->bootstrap();
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
        $_ENV['APP_ENV'] = 'test-profiler';
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
        $_ENV['APP_ENV'] = 'test-profiler';
        $_ENV['PROFILER_ENABLED'] = false;
        $this->app = $this->app();

        $config = $this->app->make('config');

        $this->assertFalse($config->get('profiler.enabled'));
        $this->assertInstanceOf(DisabledProfiler::class, $this->app->make(Profiler::class));
    }

    /** @test */
    function profiler_can_be_disabled_in_config_file_for_specific_environment()
    {
        putenv('APP_ENV=local');
        putenv('PROFILER_ENABLED=true');
        $_ENV['APP_ENV'] = 'local';
        $_ENV['PROFILER_ENABLED'] = true;
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.enabled_overrides', ['local' => false]);
        });

        $this->assertInstanceOf(DisabledProfiler::class, $this->app->make(Profiler::class));
    }

    /** @test */
    function enabled_profiler_tracks_laravel()
    {
        $timer = Mockery::spy(Timer::class);
        $dataTracker = Mockery::spy(DataTracker::class);
        $dataProcessor = Mockery::spy(DataProcessor::class);
        $httpRequestHandledListener = Mockery::spy(HttpRequestHandledListener::class);
        $consoleCommandFinishedListener = Mockery::spy(ConsoleCommandFinishedListener::class);

        $this->app = $this->appBeforeBootstrap();
        $this->app->instance(HttpRequestHandledListener::class, $httpRequestHandledListener);
        $this->app->instance(ConsoleCommandFinishedListener::class, $consoleCommandFinishedListener);
        $this->registerServiceProviderWith($this->app, $timer, $dataTracker, $dataProcessor);

        $this->app->terminate();

        $this->assertInstanceOf(LaravelProfiler::class, $this->app->make(Profiler::class));
        $this->assertSame($timer, $this->app->make(Timer::class));
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
        $_ENV['PROFILER_ENABLED'] = false;
        $timer = Mockery::spy(Timer::class);
        $dataTracker = Mockery::spy(DataTracker::class);
        $dataProcessor = Mockery::spy(DataProcessor::class);
        $httpRequestHandledListener = Mockery::spy(HttpRequestHandledListener::class);
        $consoleCommandFinishedListener = Mockery::spy(ConsoleCommandFinishedListener::class);

        $this->app = $this->appBeforeBootstrap();
        $this->app->instance(HttpRequestHandledListener::class, $httpRequestHandledListener);
        $this->app->instance(ConsoleCommandFinishedListener::class, $consoleCommandFinishedListener);
        $this->registerServiceProviderWith($this->app, $timer, $dataTracker, $dataProcessor);

        $this->app->terminate();

        $this->assertInstanceOf(DisabledProfiler::class, $this->app->make(Profiler::class));
        $this->assertSame($timer, $this->app->make(Timer::class));
        $this->assertSame($dataTracker, $this->app->make(DataTracker::class));
        $dataTracker->shouldNotHaveReceived('track');
        $dataTracker->shouldNotHaveReceived('terminate');
        $this->assertSame($dataProcessor, $this->app->make(DataProcessor::class));
        $dataProcessor->shouldNotHaveReceived('process');
        $httpRequestHandledListener->shouldNotHaveReceived('listen');
        $consoleCommandFinishedListener->shouldNotHaveReceived('listen');
    }

    /** @test */
    function enabled_profiler_is_booted_before_all_service_providers_are_booted()
    {
        $eventsExecuted = 0;
        $this->app = $this->appBeforeBootstrap();

        $this->app->afterBootstrapping(RegisterProviders::class, function () use (&$eventsExecuted) {
            $this->app->register(ServiceProvider::class);
            $this->assertFalse($this->app->resolved(Timer::class));
            $eventsExecuted++;
        });

        $this->app->booting(function () use (&$eventsExecuted) {
            $this->assertInstanceOf(TimerService::class, $this->app->make(Timer::class));
            $eventsExecuted++;
        });

        $this->app->make(Kernel::class)->bootstrap();

        $this->assertEquals(2, $eventsExecuted);
    }

    /** @test */
    function disabled_profiler_is_booted_before_all_service_providers_are_booted()
    {
        putenv('PROFILER_ENABLED=false');
        $_ENV['PROFILER_ENABLED'] = false;
        $eventsExecuted = 0;
        $this->app = $this->appBeforeBootstrap();

        $this->app->afterBootstrapping(RegisterProviders::class, function () use (&$eventsExecuted) {
            $this->app->register(ServiceProvider::class);
            $this->assertFalse($this->app->resolved(Timer::class));
            $eventsExecuted++;
        });

        $this->app->booting(function () use (&$eventsExecuted) {
            $this->assertInstanceOf(NullTimerService::class, $this->app->make(Timer::class));
            $eventsExecuted++;
        });

        $this->app->make(Kernel::class)->bootstrap();

        $this->assertEquals(2, $eventsExecuted);
    }

    /** @test */
    function enabled_profiler_registers_terminating_callback_after_all_service_providers_are_booted()
    {
        $executedBefore = false;
        $this->app = $this->appBeforeBootstrap();

        $this->app->afterBootstrapping(RegisterProviders::class, function () use (&$order) {
            $this->app->register(ServiceProvider::class);
            $this->app->register(new class($this->app) extends \Illuminate\Support\ServiceProvider {
                public function register() {}
                public function boot() {
                    $this->app->terminating(function () {
                        event('another-executed-before', [new \stdClass()]);
                    });
                }
            });
        });

        $this->app->make(Kernel::class)->bootstrap();
        $this->turnOffProcessors();

        Event::listen('another-executed-before', function () use (&$executedBefore) {
            $executedBefore = true;
        });

        Event::listen(Terminating::class, function () use (&$executedBefore) {
            $this->assertTrue($executedBefore);
        });

        $this->app->terminate();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        putenv('APP_ENV=local');
        putenv('PROFILER_ENABLED');
        unset($_ENV['APP_ENV']);
        unset($_ENV['PROFILER_ENABLED']);
    }
}
