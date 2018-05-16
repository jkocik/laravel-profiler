<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Closure;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
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
     * @var bool
     */
    protected $isStartingExecution = false;

    /**
     * @var bool
     */
    protected $isFinishedExecution = false;

    /**
     * @param Closure $callback
     * @return void
     */
    protected function tapStartingExecutionTests(Closure $callback): void
    {
        if (! $this->isStartingExecution) {
            return;
        }

        $callback->__invoke();
    }

    /**
     * @param Closure $callback
     * @return void
     */
    protected function tapFinishedExecutionTests(Closure $callback): void
    {
        if (! $this->isFinishedExecution) {
            return;
        }

        $callback->__invoke();
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->turnOffProcessors();

        $this->executionData = $this->app->make(ExecutionData::class);

        Event::listen(\Illuminate\Console\Events\ArtisanStarting::class, function ($event) {
            $this->isStartingExecution = true;
            $this->isFinishedExecution = false;
        });

        Event::listen(\Illuminate\Console\Events\CommandFinished::class, function ($event) {
            $this->isStartingExecution = false;
            $this->isFinishedExecution = true;
        });

        $this->app->make(Kernel::class)->registerCommand(new DummyCommand($this->testExitCode));
    }

    /** @test */
    function has_console_request()
    {
        Artisan::call('dummy-command');
        $request = $this->executionData->request();

        $this->tapStartingExecutionTests(function () use ($request) {
            $this->assertInstanceOf(ConsoleStartingRequest::class, $request);
        });

        $this->tapFinishedExecutionTests(function () use ($request) {
            $this->assertInstanceOf(ConsoleFinishedRequest::class, $request);
        });
    }

    /** @test */
    function has_request_type()
    {
        Artisan::call('dummy-command');
        $request = $this->executionData->request();

        $this->tapStartingExecutionTests(function () use ($request) {
            $this->assertEquals('command-starting', $request->meta()->get('type'));
        });

        $this->tapFinishedExecutionTests(function () use ($request) {
            $this->assertEquals('command-finished', $request->meta()->get('type'));
        });
    }

    /** @test */
    function has_request_method()
    {
        Artisan::call('dummy-command');
        $request = $this->executionData->request();

        $this->tapStartingExecutionTests(function () use ($request) {
            $this->assertArrayNotHasKey('path', $request->meta());
        });

        $this->tapFinishedExecutionTests(function () use ($request) {
            $this->assertEquals('dummy-command', $request->meta()->get('path'));
        });
    }

    /** @test */
    function has_request_arguments()
    {
        Artisan::call('dummy-command', ['user' => 'Abc']);
        $request = $this->executionData->request();

        $this->tapStartingExecutionTests(function () use ($request) {
            $this->assertArrayNotHasKey('arguments', $request->data());
        });

        $this->tapFinishedExecutionTests(function () use ($request) {
            $this->assertEquals('dummy-command', $request->data()->get('arguments')['command']);
            $this->assertEquals('Abc', $request->data()->get('arguments')['user']);
        });
    }

    /** @test */
    function has_request_options()
    {
        Artisan::call('dummy-command', ['--number' => 5]);
        $request = $this->executionData->request();

        $this->tapStartingExecutionTests(function () use ($request) {
            $this->assertArrayNotHasKey('options', $request->data());
        });

        $this->tapFinishedExecutionTests(function () use ($request) {
            $this->assertEquals(5, $request->data()->get('options')['number']);
        });
    }

    /** @test */
    function has_console_response()
    {
        Artisan::call('dummy-command');
        $response = $this->executionData->response();

        $this->tapStartingExecutionTests(function () use ($response) {
            $this->assertInstanceOf(ConsoleStartingResponse::class, $response);
        });

        $this->tapFinishedExecutionTests(function () use ($response) {
            $this->assertInstanceOf(ConsoleFinishedResponse::class, $response);
        });
    }

    /** @test */
    function console_response_has_only_status()
    {
        Artisan::call('dummy-command');
        $response = $this->executionData->response();

        $this->tapStartingExecutionTests(function () use ($response) {
            $this->assertArrayHasKey('status', $response->meta());
            $this->assertNull($response->meta()->get('status'));
            $this->assertCount(1, $response->meta());
            $this->assertCount(0, $response->data());
        });

        $this->tapFinishedExecutionTests(function () use ($response) {
            $this->assertEquals($this->testExitCode, $response->meta()->get('status'));
            $this->assertCount(1, $response->meta());
            $this->assertCount(0, $response->data());
        });
    }
}
