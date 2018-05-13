<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\LaravelExecution\HttpRequest;
use JKocik\Laravel\Profiler\LaravelExecution\NullRequest;
use JKocik\Laravel\Profiler\LaravelExecution\HttpResponse;
use JKocik\Laravel\Profiler\LaravelExecution\NullResponse;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyCommand;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleStartingRequest;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleFinishedRequest;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleStartingResponse;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleFinishedResponse;

class LaravelExecutionTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->turnOffProcessors();
    }

    /** @test */
    function laravel_execution_data_is_singleton()
    {
        $executionDataA = $this->app->make(ExecutionData::class);
        $executionDataB = $this->app->make(ExecutionData::class);

        $this->assertSame($executionDataA, $executionDataB);
    }

    /** @test */
    function app_without_http_and_command_execution_has_null_request_and_response()
    {
        $executionData = $this->app->make(ExecutionData::class);
        $request = $executionData->request();
        $response = $executionData->response();

        $this->assertInstanceOf(NullRequest::class, $request);
        $this->assertArrayHasKey('type', $request->meta());
        $this->assertNull($request->meta()->get('type'));
        $this->assertArrayHasKey('method', $request->meta());
        $this->assertNull($request->meta()->get('method'));
        $this->assertArrayHasKey('path', $request->meta());
        $this->assertNull($request->meta()->get('path'));
        $this->assertCount(0, $request->data());

        $this->assertInstanceOf(NullResponse::class, $response);
        $this->assertArrayHasKey('status', $response->meta());
        $this->assertNull($response->meta()->get('status'));
        $this->assertCount(0, $response->data());
    }

    /** @test */
    function app_with_http_execution_has_http_request_and_response()
    {
        $executionData = $this->app->make(ExecutionData::class);

        $this->post('/abc', []);
        $request = $executionData->request();
        $response = $executionData->response();

        $this->assertInstanceOf(HttpRequest::class, $request);
        $this->assertEquals('http', $request->meta()->get('type'));
        $this->assertEquals('POST', $request->meta()->get('method'));
        $this->assertEquals('abc', $request->meta()->get('path'));

        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertEquals(404, $response->meta()->get('status'));
    }

    /** @test */
    function http_request_can_have_http_type_ajax_suffix()
    {
        $executionData = $this->app->make(ExecutionData::class);

        $this->post('/abc', [], ['X-Requested-With' => 'XMLHttpRequest']);
        $request = $executionData->request();

        $this->assertEquals('http / ajax', $request->meta()->get('type'));
    }

    /** @test */
    function http_request_can_have_http_type_pjax_suffix()
    {
        $executionData = $this->app->make(ExecutionData::class);

        $this->post('/abc', [], ['X-PJAX' => true]);
        $request = $executionData->request();

        $this->assertEquals('http / pjax', $request->meta()->get('type'));
    }

    /** @test */
    function http_request_can_have_http_type_json_suffix()
    {
        $executionData = $this->app->make(ExecutionData::class);

        $this->post('/abc', [], ['Content-Type' => 'application/json']);
        $request = $executionData->request();

        $this->assertEquals('http / json', $request->meta()->get('type'));
    }

    /** @test */
    function http_request_can_mix_http_type_suffixes()
    {
        $executionData = $this->app->make(ExecutionData::class);

        $this->post('/abc', [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Content-Type' => 'application/json',
        ]);
        $request = $executionData->request();

        $this->assertEquals('http / ajax / json', $request->meta()->get('type'));
    }

    /** @test */
    function console_execution_has_console_request_and_response()
    {
        if ($this->laravelVersionLowerThan(5.5)) {
            return;
        }

        $executionData = $this->app->make(ExecutionData::class);

        $this->app->make(Kernel::class)->registerCommand(new DummyCommand(5));
        Artisan::call('dummy-command');
        $request = $executionData->request();
        $response = $executionData->response();

        $this->assertInstanceOf(ConsoleFinishedRequest::class, $request);
        $this->assertEquals('command', $request->meta()->get('type'));
        $this->assertArrayHasKey('method', $request->meta());
        $this->assertNull($request->meta()->get('method'));
        $this->assertEquals('dummy-command', $request->meta()->get('path'));

        $this->assertInstanceOf(ConsoleFinishedResponse::class, $response);
        $this->assertEquals(5, $response->meta()->get('status'));
    }

    /** @test */
    function in_old_laravel_versions_console_execution_has_console_request_and_response()
    {
        if ($this->laravelVersionEqualOrGreaterThan(5.5)) {
            return;
        }

        $executionData = $this->app->make(ExecutionData::class);

        $this->app->make(Kernel::class)->registerCommand(new DummyCommand());
        Artisan::call('dummy-command');
        $request = $executionData->request();
        $response = $executionData->response();

        $this->assertInstanceOf(ConsoleStartingRequest::class, $request);
        $this->assertEquals('command', $request->meta()->get('type'));
        $this->assertArrayHasKey('method', $request->meta());
        $this->assertNull($request->meta()->get('method'));
        $this->assertArrayHasKey('path', $request->meta());
        $this->assertNull($request->meta()->get('path'));

        $this->assertInstanceOf(ConsoleStartingResponse::class, $response);
        $this->assertArrayHasKey('status', $response->meta());
        $this->assertNull($response->meta()->get('status'));
    }
}
