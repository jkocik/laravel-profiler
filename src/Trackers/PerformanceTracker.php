<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Timer;

class PerformanceTracker extends BaseTracker
{
    /**
     * @var Timer
     */
    protected $timer;

    /**
     * PerformanceTracker constructor.
     * @param Application $app
     * @param Timer $timer
     */
    public function __construct(Application $app, Timer $timer)
    {
        parent::__construct($app);

        $this->timer = $timer;
    }


    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->data->put('performance', Collection::make([
            'timer' => $this->timer->all(),
        ]));
    }
}
