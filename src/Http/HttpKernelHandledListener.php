<?php

namespace JKocik\Laravel\Profiler\Http;

use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Contracts\DataService;
use JKocik\Laravel\Profiler\Contracts\RequestHandledListener;

class HttpKernelHandledListener implements RequestHandledListener
{
    /**
     * @var DataService
     */
    protected $dataService;

    /**
     * HttpKernelHandledListener constructor.
     * @param DataService $dataService
     */
    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        Event::listen('kernel.handled', function ($request, $response) {
            $this->dataService->setRequest($request);
            $this->dataService->setResponse($response);
        });

        Event::listen(\Illuminate\Foundation\Http\Events\RequestHandled::class, function ($event) {
            $this->dataService->setRequest($event->request);
            $this->dataService->setResponse($event->response);
        });
    }
}
