<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionRoute;
use JKocik\Laravel\Profiler\Contracts\ExecutionListener;

class HttpRequestHandledListener implements ExecutionListener
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
        Event::listen('kernel.handled', function (Request $request, Response $response) {
            $this->executionData->setRequest(new HttpRequest($request));
            $this->executionData->setRoute($this->routeOf($request));
            $this->executionData->setSession(new HttpSession(session()));
            $this->executionData->setResponse(new HttpResponse($response));
        });

        Event::listen(\Illuminate\Foundation\Http\Events\RequestHandled::class, function ($event) {
            $this->executionData->setRequest(new HttpRequest($event->request));
            $this->executionData->setRoute($this->routeOf($event->request));
            $this->executionData->setSession(new HttpSession(session()));
            $this->executionData->setResponse(new HttpResponse($event->response));
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
