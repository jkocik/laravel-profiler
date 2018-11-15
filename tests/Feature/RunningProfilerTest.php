<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\TrackerB;
use JKocik\Laravel\Profiler\Processors\BroadcastingProcessor;
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
        $config = $this->app->make(ConfigService::class);
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')->withArgs(function ($arg1, $arg2, $arg3) use ($config) {
            return $arg1 === 'POST'
                && $arg2 === $config->serverHttpConnectionUrl()
                && is_array($arg3['json']['meta'])
                && is_array($arg3['json']['data']);
        })->once()->andReturn(new Response());

        $this->app->instance(Client::class, $client);

        $this->app->terminate();

        $this->assertSame($client, $this->app->make(Client::class));
    }

    /** @test */
    function collected_data_are_not_processed_for_debugbar()
    {
        $client = Mockery::mock(Client::class);
        $client->shouldNotReceive('request');

        $this->app->instance(Client::class, $client);

        $this->get('_debugbar/assets/javascript');
    }

    /** @test */
    function collected_data_are_not_processed_for_telescope()
    {
        $client = Mockery::mock(Client::class);
        $client->shouldNotReceive('request');

        $this->app->instance(Client::class, $client);

        $this->get('telescope/telescope-api/models');
    }

    /** @test */
    function processors_exceptions_are_caught_and_logged_if_configured()
    {
        $processor = Mockery::mock(ProcessorA::class);
        $processor->shouldReceive('process')->once();

        $this->app = $this->appWith(function (Application $app) use ($processor) {
            $app->make('config')->set('profiler.handle_exceptions', 1);
            $app->make('config')->set('profiler.processors', [
                BroadcastingProcessor::class,
                BroadcastingProcessor::class,
                ProcessorA::class,
            ]);
            $app->singleton(ProcessorA::class, function () use ($processor) {
                return $processor;
            });
        });

        Log::shouldReceive('error')
            ->times(2)
            ->with(Exception::class);

        $this->app->terminate();
    }

    /** @test */
    function processors_exceptions_are_thrown_if_configured()
    {
        $processor = Mockery::spy(ProcessorA::class);

        $this->app = $this->appWith(function (Application $app) use ($processor) {
            $app->make('config')->set('profiler.handle_exceptions', 666);
            $app->make('config')->set('profiler.processors', [
                BroadcastingProcessor::class,
                ProcessorA::class,
            ]);
            $app->singleton(ProcessorA::class, function () use ($processor) {
                return $processor;
            });
        });

        try {
            $this->app->terminate();
        } catch (Exception $e) {
            $processor->shouldNotHaveReceived('process');
            return;
        }

        $this->fail('Exception should be thrown');
    }

    /** @test */
    function processors_exceptions_are_caught_and_not_logged_if_configured()
    {
        $processor = Mockery::mock(ProcessorA::class);
        $processor->shouldReceive('process')->once();

        $this->app = $this->appWith(function (Application $app) use ($processor) {
            $app->make('config')->set('profiler.handle_exceptions', 0);
            $app->make('config')->set('profiler.processors', [
                BroadcastingProcessor::class,
                BroadcastingProcessor::class,
                ProcessorA::class,
            ]);
            $app->singleton(ProcessorA::class, function () use ($processor) {
                return $processor;
            });
        });

        Log::shouldReceive('error')
            ->times(0);

        $this->app->terminate();
    }

    /** @test */
    function processors_exceptions_are_caught_and_not_logged_if_configured_incorrectly()
    {
        $processor = Mockery::mock(ProcessorA::class);
        $processor->shouldReceive('process')->once();

        $this->app = $this->appWith(function (Application $app) use ($processor) {
            $app->make('config')->set('profiler.handle_exceptions', -1);
            $app->make('config')->set('profiler.processors', [
                BroadcastingProcessor::class,
                BroadcastingProcessor::class,
                ProcessorA::class,
            ]);
            $app->singleton(ProcessorA::class, function () use ($processor) {
                return $processor;
            });
        });

        Log::shouldReceive('error')
            ->times(0);

        $this->app->terminate();
    }
}
