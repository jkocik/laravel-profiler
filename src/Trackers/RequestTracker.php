<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;

class RequestTracker extends BaseTracker
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * RequestTracker constructor.
     * @param Application $app
     * @param ExecutionData $executionData
     */
    public function __construct(Application $app, ExecutionData $executionData)
    {
        parent::__construct($app);

        $this->executionData = $executionData;
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $request = $this->executionData->request();

        $this->meta = $request->meta();
        $this->data->put('request', $request->data());
    }
}
