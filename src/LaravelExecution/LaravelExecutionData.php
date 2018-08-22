<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionRoute;
use JKocik\Laravel\Profiler\Contracts\ExecutionServer;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;
use JKocik\Laravel\Profiler\Contracts\ExecutionContent;
use JKocik\Laravel\Profiler\Contracts\ExecutionSession;
use JKocik\Laravel\Profiler\Contracts\ExecutionResponse;

class LaravelExecutionData implements ExecutionData
{
    /**
     * @var ExecutionRequest
     */
    protected $request;

    /**
     * @var ExecutionRoute
     */
    protected $route;

    /**
     * @var ExecutionSession
     */
    protected $session;

    /**
     * @var ExecutionServer
     */
    protected $server;

    /**
     * @var ExecutionResponse
     */
    protected $response;

    /**
     * @var ExecutionContent
     */
    protected $content;

    /**
     * LaravelExecutionData constructor.
     */
    public function __construct()
    {
        $this->setRequest(new NullRequest());
        $this->setRoute(new NullRoute());
        $this->setSession(new NullSession());
        $this->setServer(new NullServer());
        $this->setResponse(new NullResponse());
        $this->setContent(new NullContent());
    }

    /**
     * @param ExecutionRequest $request
     * @return void
     */
    public function setRequest(ExecutionRequest $request): void
    {
        $this->request = $request;
    }

    /**
     * @return ExecutionRequest
     */
    public function request(): ExecutionRequest
    {
        return $this->request;
    }

    /**
     * @param ExecutionRoute $route
     * @return void
     */
    public function setRoute(ExecutionRoute $route): void
    {
        $this->route = $route;
    }

    /**
     * @return ExecutionRoute
     */
    public function route(): ExecutionRoute
    {
        return $this->route;
    }

    /**
     * @param ExecutionSession $session
     * @return void
     */
    public function setSession(ExecutionSession $session): void
    {
        $this->session = $session;
    }

    /**
     * @return ExecutionSession
     */
    public function session(): ExecutionSession
    {
        return $this->session;
    }

    /**
     * @param ExecutionServer $server
     * @return void
     */
    public function setServer(ExecutionServer $server): void
    {
        $this->server = $server;
    }

    /**
     * @return ExecutionServer
     */
    public function server(): ExecutionServer
    {
        return $this->server;
    }

    /**
     * @param ExecutionResponse $response
     * @return void
     */
    public function setResponse(ExecutionResponse $response): void
    {
        $this->response = $response;
    }

    /**
     * @return ExecutionResponse
     */
    public function response(): ExecutionResponse
    {
        return $this->response;
    }

    /**
     * @param ExecutionContent $content
     * @return void
     */
    public function setContent(ExecutionContent $content): void
    {
        $this->content = $content;
    }

    /**
     * @return ExecutionContent
     */
    public function content(): ExecutionContent
    {
        return $this->content;
    }
}
