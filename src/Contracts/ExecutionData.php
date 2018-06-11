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
     * @param ExecutionRoute $route
     * @return void
     */
    public function setRoute(ExecutionRoute $route): void;

    /**
     * @return ExecutionRoute
     */
    public function route(): ExecutionRoute;

    /**
     * @param ExecutionSession $session
     * @return void
     */
    public function setSession(ExecutionSession $session): void;

    /**
     * @return ExecutionSession
     */
    public function session(): ExecutionSession;

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
