<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionRoute;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;
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
     * @var ExecutionResponse
     */
    protected $response;

    /**
     * LaravelExecutionData constructor.
     */
    public function __construct()
    {
        $this->setRequest(new NullRequest());
        $this->setRoute(new NullRoute());
        $this->setResponse(new NullResponse());
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
}
