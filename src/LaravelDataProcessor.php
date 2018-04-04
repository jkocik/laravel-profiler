<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\Processor;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;

class LaravelDataProcessor implements DataProcessor
{
    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        array_map(function ($processor) use ($dataTracker) {
            $this->makeProcessor($processor)->process($dataTracker);
        }, ProfilerConfig::processors());
    }

    /**
     * @param string $processor
     * @return Processor
     */
    protected function makeProcessor(string $processor): Processor
    {
        return app()->make($processor);
    }
}
