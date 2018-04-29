<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
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
        Event::listen('kernel.handled', function ($request, $response) {
            $this->executionData->setRequest(new HttpRequest($request));
            $this->executionData->setResponse(new HttpResponse($response));
        });

        Event::listen(\Illuminate\Foundation\Http\Events\RequestHandled::class, function ($event) {
            $this->executionData->setRequest(new HttpRequest($event->request));
            $this->executionData->setResponse(new HttpResponse($event->response));
        });
    }
}
