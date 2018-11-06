<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\Processor;
use JKocik\Laravel\Profiler\Contracts\DataTracker;

class PerformanceProcessor implements Processor
{
    /**
     * @var Collection
     */
    public $performance;

    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        $this->performance = $dataTracker->data()->get('performance');
    }
}
