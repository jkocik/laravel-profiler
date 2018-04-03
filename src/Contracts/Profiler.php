<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface Profiler
{
    /**
     * @param DataTracker $dataTracker
     * @param DataProcessor $dataProcessor
     * @return void
     */
    public function boot(DataTracker $dataTracker, DataProcessor $dataProcessor): void;
}
