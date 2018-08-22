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
     * @param ExecutionServer $server
     * @return void
     */
    public function setServer(ExecutionServer $server): void;

    /**
     * @return ExecutionServer
     */
    public function server(): ExecutionServer;

    /**
     * @param ExecutionResponse $response
     * @return void
     */
    public function setResponse(ExecutionResponse $response): void;

    /**
     * @return ExecutionResponse
     */
    public function response(): ExecutionResponse;

    /**
     * @param ExecutionContent $content
     * @return void
     */
    public function setContent(ExecutionContent $content): void;

    /**
     * @return ExecutionContent
     */
    public function content(): ExecutionContent;
}
