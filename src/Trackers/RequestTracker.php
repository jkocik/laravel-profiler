<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\DataService;

class RequestTracker extends BaseTracker
{
    /**
     * @var DataService
     */
    protected $dataService;

    /**
     * RequestTracker constructor.
     * @param Application $app
     * @param DataService $dataService
     */
    public function __construct(Application $app, DataService $dataService)
    {
        parent::__construct($app);

        $this->dataService = $dataService;
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $request = $this->dataService->request();

        $this->meta->put('method', $request->method());
        $this->meta->put('path', $request->path());
        $this->meta->put('is_ajax', $request->ajax());
    }
}
