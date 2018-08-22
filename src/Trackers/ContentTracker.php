<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;

class ContentTracker extends BaseTracker
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * ContentTracker constructor.
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
        $content = $this->executionData->content();

        $this->meta = $content->meta();
        $this->data->put('content', $content->data()->get('content'));
    }
}
