<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Trackers\BaseTracker;

class TrackerA extends BaseTracker
{
    /**
     * TrackerA constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->meta->put('meta-key', 'meta-value');
    }

    /**
     * @return void
     */
    public function terminate(): void
    {

    }
}
