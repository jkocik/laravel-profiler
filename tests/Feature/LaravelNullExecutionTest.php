<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\LaravelExecution\NullRequest;
use JKocik\Laravel\Profiler\LaravelExecution\NullResponse;

class LaravelNullExecutionTest extends TestCase
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->turnOffProcessors();

        $this->executionData = $this->app->make(ExecutionData::class);
    }


    /** @test */
    function has_null_request()
    {
        $request = $this->executionData->request();

        $this->assertInstanceOf(NullRequest::class, $request);
    }

    /** @test */
    function null_request_has_only_type()
    {
        $request = $this->executionData->request();

        $this->assertArrayHasKey('type', $request->meta());
        $this->assertNull($request->meta()->get('type'));
        $this->assertCount(1, $request->meta());
        $this->assertCount(0, $request->data());
    }

    /** @test */
    function has_null_response()
    {
        $response = $this->executionData->response();

        $this->assertInstanceOf(NullResponse::class, $response);
    }

    /** @test */
    function null_response_is_empty()
    {
        $response = $this->executionData->response();

        $this->assertCount(0, $response->meta());
        $this->assertCount(0, $response->data());
    }
}
