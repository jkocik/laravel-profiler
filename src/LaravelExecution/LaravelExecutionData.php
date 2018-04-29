<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;
use JKocik\Laravel\Profiler\Contracts\ExecutionResponse;

class LaravelExecutionData implements ExecutionData
{
    /**
     * @var ExecutionRequest
     */
    protected $request;

    /**
     * @var ExecutionResponse
     */
    protected $response;

    /**
     * LaravelExecutionData constructor.
     */
    public function __construct()
    {
        $this->request = new NullRequest();
        $this->response = new NullResponse();
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
