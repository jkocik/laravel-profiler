<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface ExecutionData
{
    /**
     * @param ExecutionRequest $request
     * @return void
     */
    public function setRequest(ExecutionRequest $request): void;

    /**
     * @return ExecutionRequest
     */
    public function request(): ExecutionRequest;

    /**
     * @param ExecutionResponse $response
     * @return void
     */
    public function setResponse(ExecutionResponse $response): void;

    /**
     * @return ExecutionResponse
     */
    public function response(): ExecutionResponse;
}
