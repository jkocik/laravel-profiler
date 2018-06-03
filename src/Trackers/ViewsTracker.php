<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\View\View;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;

class ViewsTracker extends BaseTracker
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

    public function terminate(): void
    {
        $views = $this->executionData->views()->map(function (View $view) {
            return [
                'name' => $view->name(),
                'path' => $view->getPath(),
                'data' => $view->getData(),
            ];
        })->values();

        $this->data->put('views', $views);
    }
}
