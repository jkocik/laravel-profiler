<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use ElephantIO\Client;
use Psr\Log\NullLogger;
use ElephantIO\EngineInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use ElephantIO\Engine\SocketIO\Version2X;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerB;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorB;

class RunningProfilerTest extends TestCase
{
    /** @test */
    function collected_data_are_processed_when_laravel_is_terminated()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.trackers', [
                TrackerA::class,
                TrackerB::class,
            ]);
            $app->make('config')->set('profiler.processors', [
                ProcessorA::class,
                ProcessorB::class,
            ]);
            $app->singleton(ProcessorA::class, function () {
               return new ProcessorA();
            });
            $app->singleton(ProcessorB::class, function () {
                return new ProcessorB();
            });
        });

        $processorA = $this->app->make(ProcessorA::class);
        $processorB = $this->app->make(ProcessorB::class);

        $this->assertNotEquals('meta-value', $processorA->meta->get('meta-key'));
        $this->assertNotEquals('meta-value', $processorB->meta->get('meta-key'));
        $this->assertNotEquals('data-value', $processorA->data->get('data-key'));
        $this->assertNotEquals('data-value', $processorB->data->get('data-key'));

        $this->app->terminate();

        $this->assertEquals('meta-value', $processorA->meta->get('meta-key'));
        $this->assertEquals('meta-value', $processorB->meta->get('meta-key'));
        $this->assertEquals('data-value', $processorA->data->get('data-key'));
        $this->assertEquals('data-value', $processorB->data->get('data-key'));
    }

    /** @test */
    function collected_data_are_broadcast_by_default()
    {
        $socketEngine = Mockery::mock(EngineInterface::class);
        $socketEngine->shouldReceive('connect')->once();
        $socketEngine->shouldReceive('close')->once();
        $socketEngine->shouldNotReceive('keepAlive');
        $socketEngine->shouldReceive('emit')->withArgs(function ($arg1, $arg2) {
            return $arg1 === 'laravel-profiler-broadcasting'
                && $arg2['meta'] instanceof Collection
                && $arg2['data'] instanceof Collection;
        })->once();

        $this->app->singleton(EngineInterface::class, function () use ($socketEngine) {
            return $socketEngine;
        });

        $this->app->terminate();

        $this->assertSame($socketEngine, $this->app->make(EngineInterface::class));
    }

    /** @test */
    function broadcasting_client_uses_version_2_of_socket_engine()
    {
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Version2X::class, $client->getEngine());
    }

    /** @test */
    function broadcasting_client_uses_null_logger()
    {
        $logger = Mockery::spy(NullLogger::class);
        $this->app->instance(NullLogger::class, $logger);

        $socketEngine = Mockery::mock(EngineInterface::class)->shouldIgnoreMissing();
        $this->app->singleton(EngineInterface::class, function () use ($socketEngine) {
            return $socketEngine;
        });

        $client = $this->app->make(Client::class);
        $client->close();

        $logger->shouldHaveReceived('debug');
    }

    /** @test */
    function broadcasting_exception_is_caught_and_not_processed_if_is_off_in_config()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.broadcasting_log_errors', false);
        });

        Log::shouldReceive('error')
            ->times(0);

        $this->app->terminate();
    }

    /** @test */
    function broadcasting_exception_is_caught_and_processed_if_is_on_in_config()
    {
        Log::shouldReceive('error')
            ->once()
            ->with(\Exception::class);

        $this->app->terminate();
    }
}
