<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use GuzzleHttp\Exception\ConnectException;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Services\ConsoleService;
use JKocik\Laravel\Profiler\Events\ServerConnectionFailed;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerB;
use JKocik\Laravel\Profiler\Processors\StatusCommandProcessor;
use JKocik\Laravel\Profiler\Events\ServerConnectionSuccessful;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use JKocik\Laravel\Profiler\Events\ProfilerServerConnectionFailed;
use JKocik\Laravel\Profiler\Events\ProfilerServerConnectionSuccessful;

class CommandsTest extends TestCase
{
    use InteractsWithConsole;

    /**
     * @return int
     */
    protected function countAllConfigurableTrackers(): int
    {
        $abstract = 1;
        $required = 4;
        $directoryDots = 2;
        $trackers = scandir(__DIR__ . '/../../src/Trackers/');

        return count($trackers) - $abstract - $required - $directoryDots;
    }

    /** @test */
    function tells_status_if_profiler_is_enabled()
    {
        $this->tapLaravelVersionFrom(5.7, function () {
            $this->artisan('profiler:status')
                ->expectsOutput('1) Your current environment is: ' . $this->app->environment())
                ->expectsOutput('Laravel Profiler is: enabled');
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });
    }

    /** @test */
    function lists_trackers_if_profiler_is_enabled()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.trackers', [
                TrackerA::class,
                TrackerB::class,
            ]);
        });

        $this->tapLaravelVersionFrom(5.7, function () {
            $this->artisan('profiler:status')
                ->expectsOutput('2) You have 2 tracker(s) turned on')
                ->expectsOutput('- ' . TrackerA::class)
                ->expectsOutput('- ' . TrackerB::class)
                ->expectsOutput("There are {$this->countAllConfigurableTrackers()} trackers available out of the box")
                ->expectsOutput('turn them on and off in profiler.php configuration file');
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });
    }

    /** @test */
    function switches_processor_to_status_processor()
    {
        $this->artisan('profiler:status');

        $configService = $this->app->make(ConfigService::class);

        $this->assertCount(1, $configService->processors());
        $this->assertEquals(StatusCommandProcessor::class, $configService->processors()->first());
    }

    /** @test */
    function status_processor_handles_when_connection_to_profiler_server_is_successful()
    {
        $this->tapLaravelVersionFrom(5.4, function () {
            Event::fake();

            $response = Mockery::mock(Response::class);
            $response->shouldReceive('getBody')->once()->andReturn('{"sockets": 1234, "clients": 9}');

            $client = Mockery::mock(Client::class);
            $client->shouldReceive('request')->once()->andReturn($response);

            $configService = Mockery::mock(ConfigService::class);
            $configService->shouldReceive('serverHttpConnectionUrl')->once();

            $processor = new StatusCommandProcessor($client, $configService);
            $processor->process($this->app->make(DataTracker::class));

            Event::assertNotDispatched(ProfilerServerConnectionFailed::class);
            Event::assertDispatched(ProfilerServerConnectionSuccessful::class, function ($event) {
                return $event->socketsPort === 1234 && $event->countClients === 9;
            });
        });

        $this->tapLaravelVersionTill(5.3, function () {
            $this->assertTrue(true);
        });
    }

    /** @test */
    function status_processor_handles_when_connection_to_profiler_server_failed()
    {
        $this->tapLaravelVersionFrom(5.4, function () {
            Event::fake();

            $client = Mockery::mock(Client::class);
            $client->shouldReceive('request')->once()->andThrows(new ConnectException('', new Request('method', '')));

            try {
                $processor = new StatusCommandProcessor($client, $this->app->make(ConfigService::class));
                $processor->process($this->app->make(DataTracker::class));
            } catch (ConnectException $e) {
                Event::assertDispatched(ProfilerServerConnectionFailed::class);
                Event::assertNotDispatched(ProfilerServerConnectionSuccessful::class);
                return;
            }

            $this->fail('ConnectException should be thrown');
        });

        $this->tapLaravelVersionTill(5.3, function () {
            $this->assertTrue(true);
        });
    }

    /** @test */
    function tells_if_connection_to_profiler_server_is_successful()
    {
        $this->tapLaravelVersionFrom(7, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });

        $this->tapLaravelVersionBetween(5.7, 6, function () {
            $realConsoleService = new ConsoleService($this->app, $this->app->make(ConfigService::class));

            $consoleService = Mockery::mock(ConsoleService::class)->shouldIgnoreMissing();
            $consoleService->shouldReceive('connectionStatusInfo')
                ->once()
                ->andReturn($realConsoleService->connectionStatusInfo());
            $consoleService->shouldReceive('connectionSuccessfulInfo')
                ->once()
                ->andReturn($realConsoleService->connectionSuccessfulInfo());
            $consoleService->shouldReceive('connectionSuccessfulSocketsInfo')
                ->once()
                ->andReturn($realConsoleService->connectionSuccessfulSocketsInfo(1234));
            $consoleService->shouldReceive('connectionSuccessfulClientsInfo')
                ->once()
                ->andReturn($realConsoleService->connectionSuccessfulClientsInfo(9));
            $consoleService->shouldNotReceive('connectionFailedInfo');
            $consoleService->shouldNotReceive('connectionStatusUnknownInfo');
            $this->app->instance(ConsoleService::class, $consoleService);

            $this->app->terminating(function () {
                event(new ProfilerServerConnectionSuccessful(1234, 9));
            });

            $this->artisan('profiler:status')
                ->expectsOutput('3) Trying to connect to Profiler Server on http://localhost:8099 ...')
                ->expectsOutput('Connected successfully')
                ->expectsOutput('Profiler Server sockets listening on port: 1234')
                ->expectsOutput("You have 9 Profiler Client(s) connected at the moment");

            $this->turnOffProcessors();
            $this->app->terminate();
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });
    }

    /** @test */
    function tells_if_connection_to_profiler_server_failed()
    {
        $this->tapLaravelVersionFrom(7, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });

        $this->tapLaravelVersionBetween(5.7, 6, function () {
            $realConsoleService = new ConsoleService($this->app, $this->app->make(ConfigService::class));

            $consoleService = Mockery::mock(ConsoleService::class)->shouldIgnoreMissing();
            $consoleService->shouldReceive('connectionStatusInfo')
                ->once()
                ->andReturn($realConsoleService->connectionStatusInfo());
            $consoleService->shouldNotReceive('connectionSuccessfulInfo');
            $consoleService->shouldReceive('connectionFailedInfo')
                ->once()
                ->andReturn($realConsoleService->connectionFailedInfo());
            $consoleService->shouldNotReceive('connectionStatusUnknownInfo');
            $this->app->instance(ConsoleService::class, $consoleService);

            $this->app->terminating(function () {
                event(new ProfilerServerConnectionFailed());
            });

            $this->artisan('profiler:status')
                ->expectsOutput('3) Trying to connect to Profiler Server on http://localhost:8099 ...')
                ->expectsOutput('Connection failed');

            $this->turnOffProcessors();
            $this->app->terminate();

            $consoleService->shouldNotHaveReceived('connectionUnknownInfo');
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });
    }

    /** @test */
    function tells_if_connection_to_profiler_server_has_unknown_status()
    {
        $this->tapLaravelVersionFrom(7, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });

        $this->tapLaravelVersionBetween(5.7, 6, function () {
            $realConsoleService = new ConsoleService($this->app, $this->app->make(ConfigService::class));

            $consoleService = Mockery::mock(ConsoleService::class)->shouldIgnoreMissing();
            $consoleService->shouldReceive('connectionStatusInfo')
                ->once()
                ->andReturn($realConsoleService->connectionStatusInfo());
            $consoleService->shouldNotReceive('connectionSuccessfulInfo');
            $consoleService->shouldNotReceive('connectionFailedInfo');
            $consoleService->shouldReceive('connectionStatusUnknownInfo')
                ->once()
                ->andReturn($realConsoleService->connectionStatusUnknownInfo());
            $this->app->instance(ConsoleService::class, $consoleService);

            $this->artisan('profiler:status')
                ->expectsOutput('3) Trying to connect to Profiler Server on http://localhost:8099 ...')
                ->expectsOutput('BroadcastingProcessor did not report connection status, connection status is unknown');

            $this->turnOffProcessors();
            $this->app->terminate();
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

        $configService = Mockery::spy(ConfigService::class)->shouldIgnoreMissing();
        $this->app->instance(ConfigService::class, $configService);

        $this->tapLaravelVersionFrom(5.7, function () {
            $this->artisan('profiler:status')
                ->expectsOutput('1) Your current environment is: ' . $this->app->environment())
                ->expectsOutput('Laravel Profiler is: disabled');
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:status');
            $this->assertTrue(true);
        });

        $configService->shouldNotHaveReceived('trackers');
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
    function can_start_profiler_server_with_configured_ports()
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

    /** @test */
    function tells_starting_message_for_profiler_server()
    {
        $this->tapLaravelVersionFrom(5.7, function () {
            $this->artisan('profiler:server')
                ->expectsOutput('Starting Profiler Server ...');
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:server');
            $this->assertTrue(true);
        });
    }

    /** @test */
    function can_start_profiler_client()
    {
        $realConsoleService = $this->app->make(ConsoleService::class);

        $consoleService = Mockery::mock(ConsoleService::class);
        $consoleService->shouldReceive('profilerClientCmd')->once()->with(false);
        $this->app->instance(ConsoleService::class, $consoleService);

        Artisan::call('profiler:client');

        $this->assertEquals(
            'node_modules/.bin/http-server node_modules/laravel-profiler-client/dist/ -o -s',
            $realConsoleService->profilerClientCmd(false)
        );
    }

    /** @test */
    function can_start_profiler_client_with_manual_option()
    {
        $consoleService = Mockery::mock(ConsoleService::class);
        $consoleService->shouldReceive('profilerClientCmd')->twice()->with(true);
        $this->app->instance(ConsoleService::class, $consoleService);

        Artisan::call('profiler:client', ['--manual' => true]);
        Artisan::call('profiler:client', ['-m' => true]);

        $realConsoleService = new ConsoleService($this->app, $this->app->make(ConfigService::class));

        $this->assertEquals(
            'node_modules/.bin/http-server node_modules/laravel-profiler-client/dist/',
            $realConsoleService->profilerClientCmd(true)
        );
    }

    /** @test */
    function tells_starting_message_for_profiler_client()
    {
        $this->tapLaravelVersionFrom(5.7, function () {
            $this->artisan('profiler:client')
                ->expectsOutput('Starting Profiler Client ...');
        });

        $this->tapLaravelVersionTill(5.6, function () {
            $this->artisan('profiler:client');
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
