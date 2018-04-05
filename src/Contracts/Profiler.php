<?php

namespace JKocik\Laravel\Profiler\Contracts;

use Illuminate\Foundation\Application;

interface Profiler
{
    /**
     * @param Application $app
     * @param DataTracker $dataTracker
     * @param DataProcessor $dataProcessor
     * @return void
     */
    public function boot(Application $app, DataTracker $dataTracker, DataProcessor $dataProcessor): void;
}
