<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\LaravelExecution\NullRoute;
use JKocik\Laravel\Profiler\LaravelExecution\NullServer;
use JKocik\Laravel\Profiler\LaravelExecution\NullContent;
use JKocik\Laravel\Profiler\LaravelExecution\NullRequest;
use JKocik\Laravel\Profiler\LaravelExecution\NullSession;
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
    protected function setUp(): void
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

        $this->assertTrue($request->meta()->has('type'));
        $this->assertNull($request->meta()->get('type'));
        $this->assertCount(1, $request->meta());
        $this->assertCount(0, $request->data());
    }

    /** @test */
    function has_null_route()
    {
        $route = $this->executionData->route();

        $this->assertInstanceOf(NullRoute::class, $route);
    }

    /** @test */
    function null_route_is_empty()
    {
        $route = $this->executionData->route();

        $this->assertCount(0, $route->meta());
        $this->assertCount(0, $route->data());
    }

    /** @test */
    function has_null_session()
    {
        $session = $this->executionData->session();

        $this->assertInstanceOf(NullSession::class, $session);
    }

    /** @test */
    function null_session_is_empty()
    {
        $session = $this->executionData->session();

        $this->assertCount(0, $session->meta());
        $this->assertCount(0, $session->data());
    }

    /** @test */
    function has_null_server()
    {
        $server = $this->executionData->server();

        $this->assertInstanceOf(NullServer::class, $server);
    }

    /** @test */
    function null_server_is_empty()
    {
        $server = $this->executionData->server();

        $this->assertCount(0, $server->meta());
        $this->assertCount(0, $server->data());
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

    /** @test */
    function has_null_content()
    {
        $content = $this->executionData->content();

        $this->assertInstanceOf(NullContent::class, $content);
    }

    /** @test */
    function null_content_is_empty()
    {
        $content = $this->executionData->content();

        $this->assertCount(0, $content->meta());
        $this->assertCount(0, $content->data());
    }
}
