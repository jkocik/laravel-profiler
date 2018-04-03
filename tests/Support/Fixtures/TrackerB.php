<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use JKocik\Laravel\Profiler\Trackers\BaseTracker;

class TrackerB extends BaseTracker
{
    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->data->put('data-key', 'data-value');
    }
}
