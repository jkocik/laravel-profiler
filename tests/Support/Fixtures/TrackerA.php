<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use JKocik\Laravel\Profiler\Trackers\BaseTracker;

class TrackerA extends BaseTracker
{
    /**
     * TrackerA constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->meta->put('meta-key', 'meta-value');
    }

    /**
     * @return void
     */
    public function terminate(): void
    {

    }
}
