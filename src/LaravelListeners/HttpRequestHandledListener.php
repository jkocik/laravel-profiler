<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionRoute;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;
use JKocik\Laravel\Profiler\LaravelExecution\NullRoute;
use JKocik\Laravel\Profiler\LaravelExecution\HttpRoute;
use JKocik\Laravel\Profiler\LaravelExecution\HttpServer;
use JKocik\Laravel\Profiler\LaravelExecution\HttpSession;
use JKocik\Laravel\Profiler\LaravelExecution\HttpContent;
use JKocik\Laravel\Profiler\LaravelExecution\HttpRequest;
use JKocik\Laravel\Profiler\LaravelExecution\HttpResponse;

class HttpRequestHandledListener implements LaravelListener
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * HttpRequestHandledListener constructor.
     * @param ExecutionData $executionData
     */
    public function __construct(ExecutionData $executionData)
    {
        $this->executionData = $executionData;
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        /** @codeCoverageIgnoreStart */
        Event::listen('kernel.handled', function (Request $request, Response $response) {
            $this->executionData->setRequest(new HttpRequest($request));
            $this->executionData->setRoute($this->routeOf($request));
            $this->executionData->setSession(new HttpSession(session()));
            $this->executionData->setServer(new HttpServer($request));
            $this->executionData->setResponse(new HttpResponse($response));
            $this->executionData->setContent(new HttpContent($response));
        });
        /** @codeCoverageIgnoreEnd */

        Event::listen(\Illuminate\Foundation\Http\Events\RequestHandled::class, function ($event) {
            $this->executionData->setRequest(new HttpRequest($event->request));
            $this->executionData->setRoute($this->routeOf($event->request));
            $this->executionData->setSession(new HttpSession(session()));
            $this->executionData->setServer(new HttpServer($event->request));
            $this->executionData->setResponse(new HttpResponse($event->response));
            $this->executionData->setContent(new HttpContent($event->response));
        });
    }

    /**
     * @param Request $request
     * @return ExecutionRoute
     */
    protected function routeOf(Request $request): ExecutionRoute
    {
        return $request->route() ? new HttpRoute($request->route()) : new NullRoute();
    }
}
