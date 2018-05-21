<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;

class SessionTracker extends BaseTracker
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
        $session = $this->executionData->session();

        $this->meta = $session->meta();
        $this->data->put('session', $session->data());
    }
}
