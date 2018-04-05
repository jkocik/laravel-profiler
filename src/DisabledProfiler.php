<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;

class DisabledProfiler implements Profiler
{
    /**
     * @param Application $app
     * @param DataTracker $dataTracker
     * @param DataProcessor $dataProcessor
     * @return void
     */
    public function boot(Application $app, DataTracker $dataTracker, DataProcessor $dataProcessor): void
    {

    }
}
