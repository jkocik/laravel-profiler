<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\Profiler;

class LaravelProfiler implements Profiler
{
    /**
     * @param DataTracker $dataTracker
     * @param DataProcessor $dataProcessor
     * @return void
     */
    public function boot(DataTracker $dataTracker, DataProcessor $dataProcessor): void
    {
        $dataTracker->track();

        $dataProcessor->process();
    }
}
