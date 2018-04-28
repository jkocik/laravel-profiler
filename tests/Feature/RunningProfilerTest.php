<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Mockery;
use ElephantIO\Client;
use ElephantIO\EngineInterface;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use ElephantIO\Engine\SocketIO\Version2X;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\DataService;
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
    function app_without_request_execution_has_fake_request()
    {
        $request = $this->app->make(DataService::class)->request();

        $this->assertNull($request->method());
        $this->assertNull($request->path());
        $this->assertNull($request->ajax());
    }

    /** @test */
    function app_with_request_execution_has_real_request()
    {
        $this->turnOffProcessors();
        $dataService = $this->app->make(DataService::class);

        $this->post('/666', []);

        $this->assertEquals('POST', $dataService->request()->method());
        $this->assertEquals('666', $dataService->request()->path());
        $this->assertFalse($dataService->request()->ajax());
    }

    /** @test */
    function app_without_request_execution_has_fake_response()
    {
        $response = $this->app->make(DataService::class)->response();

        $this->assertNull($response->status());
    }

    /** @test */
    function app_with_request_execution_has_real_response()
    {
        $this->turnOffProcessors();
        $dataService = $this->app->make(DataService::class);

        $this->post('/666', []);

        $this->assertEquals('404', $dataService->response()->status());
    }
}
