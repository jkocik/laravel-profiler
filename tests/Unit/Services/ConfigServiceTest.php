<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Services;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Services\LogService;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerB;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorB;

class ConfigServiceTest extends TestCase
{
    /** @test */
    function returns_trackers()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.trackers', [
                TrackerA::class,
                TrackerB::class,
            ]);
        });

        $trackers = $this->app->make(ConfigService::class)->trackers();

        $this->assertTrue($trackers->contains(TrackerA::class));
        $this->assertTrue($trackers->contains(TrackerB::class));
    }

    /** @test */
    function returns_processors()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.processors', [
                ProcessorA::class,
                ProcessorB::class,
            ]);
        });

        $processors = $this->app->make(ConfigService::class)->processors();

        $this->assertTrue($processors->contains(ProcessorA::class));
        $this->assertTrue($processors->contains(ProcessorB::class));
    }

    /** @test */
    function returns_server_http_connection_url()
    {
        $url = $this->app->make(ConfigService::class)->serverHttpConnectionUrl();

        $this->assertEquals('http://localhost:8099', $url);
    }

    /** @test */
    function returns_server_http_port()
    {
        $port = $this->app->make(ConfigService::class)->serverHttpPort();

        $this->assertEquals('8099', $port);
    }

    /** @test */
    function returns_server_sockets_port()
    {
        $port = $this->app->make(ConfigService::class)->serverSocketsPort();

        $this->assertEquals('1901', $port);
    }

    /** @test */
    function returns_handle_exceptions()
    {
        $handleDefault = $this->app->make(ConfigService::class)->handleExceptions(
            LogService::HANDLE_EXCEPTIONS_LOG
        );

        $this->assertTrue($handleDefault);
    }
}
