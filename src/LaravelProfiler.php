<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\Contracts\ExecutionWatcher;
use JKocik\Laravel\Profiler\Contracts\RequestHandledListener;

class LaravelProfiler implements Profiler
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var DataTracker
     */
    protected $dataTracker;

    /**
     * @var DataProcessor
     */
    protected $dataProcessor;

    /**
     * @var ExecutionWatcher
     */
    protected $executionWatcher;

    /**
     * LaravelProfiler constructor.
     * @param Application $app
     * @param DataTracker $dataTracker
     * @param DataProcessor $dataProcessor
     * @param ExecutionWatcher $executionWatcher
     */
    public function __construct(
        Application $app,
        DataTracker $dataTracker,
        DataProcessor $dataProcessor,
        ExecutionWatcher $executionWatcher
    ) {
        $this->app = $app;
        $this->dataTracker = $dataTracker;
        $this->dataProcessor = $dataProcessor;
        $this->executionWatcher = $executionWatcher;

    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->executionWatcher->watch();

        $this->dataTracker->track();

        $this->app->terminating(function () {
            $this->dataTracker->terminate();
            $this->dataProcessor->process($this->dataTracker);
        });
    }
}
