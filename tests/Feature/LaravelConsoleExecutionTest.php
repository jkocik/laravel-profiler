<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\LaravelExecution\NullRoute;
use JKocik\Laravel\Profiler\LaravelExecution\NullServer;
use JKocik\Laravel\Profiler\LaravelExecution\NullContent;
use JKocik\Laravel\Profiler\LaravelExecution\NullSession;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyCommand;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleFinishedRequest;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleStartingRequest;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleFinishedResponse;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleStartingResponse;

class LaravelConsoleExecutionTest extends TestCase
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * @var int
     */
    protected $testExitCode = 123;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->turnOffProcessors();

        $this->executionData = $this->app->make(ExecutionData::class);

        $this->app->make(Kernel::class)->registerCommand(new DummyCommand($this->testExitCode));
    }

    /** @test */
    function has_console_request()
    {
        Artisan::call('dummy-command');
        $request = $this->executionData->request();

        $this->tapLaravelVersionTill(5.4, function () use ($request) {
            $this->assertInstanceOf(ConsoleStartingRequest::class, $request);
        });

        $this->tapLaravelVersionFrom(5.5, function () use ($request) {
            $this->assertInstanceOf(ConsoleFinishedRequest::class, $request);
        });
    }

    /** @test */
    function has_request_type()
    {
        Artisan::call('dummy-command');
        $request = $this->executionData->request();

        $this->tapLaravelVersionTill(5.4, function () use ($request) {
            $this->assertEquals('command-starting', $request->meta()->get('type'));
        });

        $this->tapLaravelVersionFrom(5.5, function () use ($request) {
            $this->assertEquals('command-finished', $request->meta()->get('type'));
        });
    }

    /** @test */
    function has_request_method()
    {
        Artisan::call('dummy-command');
        $request = $this->executionData->request();

        $this->tapLaravelVersionTill(5.4, function () use ($request) {
            $this->assertArrayNotHasKey('path', $request->meta());
        });

        $this->tapLaravelVersionFrom(5.5, function () use ($request) {
            $this->assertEquals('dummy-command', $request->meta()->get('path'));
        });
    }

    /** @test */
    function has_request_arguments()
    {
        Artisan::call('dummy-command', ['user' => 'Abc']);
        $request = $this->executionData->request();

        $this->tapLaravelVersionTill(5.4, function () use ($request) {
            $this->assertArrayNotHasKey('arguments', $request->data());
        });

        $this->tapLaravelVersionFrom(5.5, function () use ($request) {
            $this->assertEquals('dummy-command', $request->data()->get('arguments')['command']);
            $this->assertEquals('Abc', $request->data()->get('arguments')['user']);
        });
    }

    /** @test */
    function has_request_options()
    {
        Artisan::call('dummy-command', ['--number' => 5]);
        $request = $this->executionData->request();

        $this->tapLaravelVersionTill(5.4, function () use ($request) {
            $this->assertArrayNotHasKey('options', $request->data());
        });

        $this->tapLaravelVersionFrom(5.5, function () use ($request) {
            $this->assertEquals(5, $request->data()->get('options')['number']);
        });
    }

    /** @test */
    function has_null_route()
    {
        Artisan::call('dummy-command');
        $route = $this->executionData->route();

        $this->assertInstanceOf(NullRoute::class, $route);
    }

    /** @test */
    function has_null_session()
    {
        Artisan::call('dummy-command');
        $session = $this->executionData->session();

        $this->assertInstanceOf(NullSession::class, $session);
    }

    /** @test */
    function has_null_server()
    {
        Artisan::call('dummy-command');
        $server = $this->executionData->server();

        $this->assertInstanceOf(NullServer::class, $server);
    }

    /** @test */
    function has_console_response()
    {
        Artisan::call('dummy-command');
        $response = $this->executionData->response();

        $this->tapLaravelVersionTill(5.4, function () use ($response) {
            $this->assertInstanceOf(ConsoleStartingResponse::class, $response);
        });

        $this->tapLaravelVersionFrom(5.5, function () use ($response) {
            $this->assertInstanceOf(ConsoleFinishedResponse::class, $response);
        });
    }

    /** @test */
    function console_response_has_only_status()
    {
        Artisan::call('dummy-command');
        $response = $this->executionData->response();

        $this->tapLaravelVersionTill(5.4, function () use ($response) {
            $this->assertArrayHasKey('status', $response->meta());
            $this->assertNull($response->meta()->get('status'));
            $this->assertCount(1, $response->meta());
            $this->assertCount(0, $response->data());
        });

        $this->tapLaravelVersionFrom(5.5, function () use ($response) {
            $this->assertEquals($this->testExitCode, $response->meta()->get('status'));
            $this->assertCount(1, $response->meta());
            $this->assertCount(0, $response->data());
        });
    }

    /** @test */
    function has_null_content()
    {
        Artisan::call('dummy-command');
        $content = $this->executionData->content();

        $this->assertInstanceOf(NullContent::class, $content);
    }
}
