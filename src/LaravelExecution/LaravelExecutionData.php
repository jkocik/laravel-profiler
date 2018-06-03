<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

Use Illuminate\View\View;
use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionRoute;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;
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
     * @var ExecutionResponse
     */
    protected $response;

    /**
     * @var Collection
     */
    protected $views;

    /**
     * LaravelExecutionData constructor.
     */
    public function __construct()
    {
        $this->setRequest(new NullRequest());
        $this->setRoute(new NullRoute());
        $this->setSession(new NullSession());
        $this->setResponse(new NullResponse());

        $this->views = Collection::make();
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
     * @param View $view
     * @return void
     */
    public function pushView(View $view): void
    {
        $this->views->push($view);
    }

    /**
     * @return Collection
     */
    public function views(): Collection
    {
        return $this->views;
    }
}
