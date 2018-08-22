<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;

class ServerTracker extends BaseTracker
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * ServerTracker constructor.
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
        $server = $this->executionData->server();

        $this->meta = $server->meta();
        $this->data->put('server', $server->data());
    }
}
