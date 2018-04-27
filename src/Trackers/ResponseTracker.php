<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\DataService;

class ResponseTracker extends BaseTracker
{
    /**
     * @var DataService
     */
    protected $dataService;

    /**
     * ResponseTracker constructor.
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
        $response = $this->dataService->response();

        $this->meta->put('status', $response->status());
    }
}
